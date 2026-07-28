<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('tienda.index');
        return view('checkout', compact('carrito'));
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'cedula'       => 'required|string|max:30',
            'departamento' => 'required|string',
            'ciudad'       => 'required|string',
            'barrio'       => 'required|string|max:150',
            'direccion'    => 'required|string|max:255',
            'telefono'     => 'required|string|max:20',
            'indicaciones' => 'nullable|string|max:255',
        ], [
            'cedula.required'    => 'El documento de identidad / cédula es obligatorio.',
            'barrio.required'    => 'El barrio es obligatorio para la entrega.',
            'direccion.required' => 'La dirección principal es obligatoria.',
            'telefono.required'  => 'El número de teléfono es obligatorio.',
        ]);

        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('tienda.index')->with('error', 'El carrito está vacío.');
        }

        $pedidoId = DB::transaction(function () use ($request, $carrito) {
            $total = 0;
            foreach ($carrito as $item) {
                $precio   = $item['precio'] ?? $item['precio_unitario'] ?? 0;
                $cantidad = $item['cantidad'] ?? $item['cant'] ?? 1;
                $total   += ((float)$precio * (int)$cantidad);
            }

            $user = Auth::user();

            $idPedido = DB::table('pedidos')->insertGetId([
                'usuario_id'   => Auth::id(),
                'total'        => $total,
                'cedula'       => $request->input('cedula'),
                'telefono'     => $request->input('telefono', $user->telefono ?? null),
                'direccion'    => $request->input('direccion'),
                'barrio'       => $request->input('barrio'),
                'indicaciones' => $request->input('indicaciones'),
                'ciudad'       => $request->input('ciudad'),
                'departamento' => $request->input('departamento'),
                'created_at'   => now(),
                'updated_at'   => now()
            ]);

            foreach ($carrito as $key => $item) {
                $productoId = $item['id'] ?? $item['producto_id'] ?? null;
                if (!$productoId) {
                    preg_match('/\d+/', (string)$key, $matches);
                    $productoId = $matches[0] ?? null;
                }

                $cantidad = (int)($item['cantidad'] ?? 1);
                $talla    = (string)($item['talla'] ?? 'N/A');
                $precio   = $item['precio'] ?? 0;

                DB::table('detalle_pedido')->insert([
                    'pedido_id'       => $idPedido,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'talla'           => $talla,
                    'precio_unitario' => $precio,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);

                // --- DESCUENTO REAL DE STOCK EN CHECKOUT ---
                if ($productoId && $talla !== 'N/A') {
                    // 1. Descontar stock en la talla específica
                    $tallaObj = DB::table('tallas')->where('numero', $talla)->orWhere('id', $talla)->first();
                    $tallaId  = $tallaObj ? $tallaObj->id : $talla;

                    DB::table('producto_talla')
                        ->where('producto_id', $productoId)
                        ->where('talla_id', $tallaId)
                        ->decrement('stock', $cantidad);

                    // 2. Descontar o verificar el producto principal
                    $stockRestante = DB::table('producto_talla')
                        ->where('producto_id', $productoId)
                        ->sum('stock');

                    if ($stockRestante <= 0) {
                        DB::table('productos')->where('id', $productoId)->update(['activo' => 0]);
                    }
                }
            }

            return $idPedido;
        });

        session()->put('ultimo_pedido_id', $pedidoId);
        return redirect()->route('checkout.pago_pantalla');
    }

    public function pagoPantalla()
    {
        $pedidoId = session()->get('ultimo_pedido_id');
        if (!$pedidoId) return redirect()->route('tienda.index');
        return view('checkout_pago', compact('pedidoId'));
    }

    public function guardarPago(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|mimes:jpeg,png,jpg,pdf|max:5120',
            'pedido_id'   => 'required'
        ]);
        
        $cloudName    = env('CLOUDINARY_CLOUD_NAME', 'x5lp98vz');
        $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', 'sneakerslh_preset');
        $file         = $request->file('comprobante');

        $extension    = strtolower($file->getClientOriginalExtension());
        $resourceType = ($extension === 'pdf') ? 'raw' : 'image';
        $comprobanteUrl = null;

        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/upload", [
                    'upload_preset' => $uploadPreset,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $comprobanteUrl = $data['secure_url'] ?? null;
            }
        } catch (\Exception $e) {
            $comprobanteUrl = null;
        }

        if (!$comprobanteUrl) {
            try {
                $path = $file->store('comprobantes', 'public');
                $comprobanteUrl = asset('storage/' . $path);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error de conexión al subir el comprobante. Intenta nuevamente.');
            }
        }

        DB::table('pagos')->insert([
            'pedido_id'       => $request->pedido_id,
            'metodo_pago'     => 'Transferencia / Nequi / Daviplata',
            'comprobante_url' => $comprobanteUrl,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);

        session()->forget('carrito');
        return redirect()->route('tienda.index')->with('success', '¡Comprobante enviado exitosamente!');
    }
}