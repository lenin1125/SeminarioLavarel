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
        $carrito = session()->get('carrito', []);
        $total = 0;
        foreach($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $user = Auth::user();
        $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');

        $datosPedido = [
            'usuario_id' => Auth::id(),
            'total'      => $total,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if (in_array('telefono', $columnasPedidos)) {
            $datosPedido['telefono'] = $request->input('telefono', $user->telefono ?? null);
        }
        if (in_array('direccion', $columnasPedidos)) {
            $datosPedido['direccion'] = $request->input('direccion', 'Dirección no especificada');
        }
        if (in_array('ciudad', $columnasPedidos)) {
            $datosPedido['ciudad'] = $request->input('ciudad', 'N/A');
        }
        if (in_array('departamento', $columnasPedidos)) {
            $datosPedido['departamento'] = $request->input('departamento', 'N/A');
        }

        $pedidoId = DB::table('pedidos')->insertGetId($datosPedido);

        foreach($carrito as $item) {
            DB::table('detalle_pedido')->insert([
                'pedido_id'       => $pedidoId,
                'producto_id'     => $item['id'],
                'cantidad'        => $item['cantidad'],
                'talla'           => $item['talla'],
                'precio_unitario' => $item['precio']
            ]);
        }
        
        session()->put('ultimo_pedido_id', $pedidoId);
        return redirect()->route('checkout.pago_pantalla');
    }

    public function pagoPantalla()
    {
        $pedidoId = session()->get('ultimo_pedido_id');
        if(!$pedidoId) return redirect()->route('tienda.index');
        return view('checkout_pago', compact('pedidoId'));
    }

    public function guardarPago(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);
        
        $cloudName = 'x5lp98vz';
        $uploadPreset = 'sneakerslh_preset';
        $file = $request->file('comprobante');

        $extension = strtolower($file->getClientOriginalExtension());
        $resourceType = ($extension === 'pdf') ? 'raw' : 'image';

        $response = Http::attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/upload", [
                'upload_preset' => $uploadPreset,
            ]);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Error al subir el comprobante. Verifica que el archivo no esté corrupto.');
        }

        $data = $response->json();

        $columnas = DB::getSchemaBuilder()->getColumnListing('pagos');
        
        $columnaImagen = 'comprobante'; 
        if (in_array('comprobante_pago', $columnas)) $columnaImagen = 'comprobante_pago';
        elseif (in_array('imagen', $columnas)) $columnaImagen = 'imagen';
        elseif (in_array('url', $columnas)) $columnaImagen = 'url';
        elseif (in_array('comprobante_url', $columnas)) $columnaImagen = 'comprobante_url';

        $insertData = [
            'pedido_id'    => $request->pedido_id,
            'metodo_pago'  => 'Transferencia / Nequi / Daviplata',
            $columnaImagen => $data['secure_url'],
            'created_at'   => now(),
            'updated_at'   => now()
        ];

        if (in_array('fecha_pago', $columnas)) {
            $insertData['fecha_pago'] = now();
        }

        DB::table('pagos')->insert($insertData);

        session()->forget('carrito');
        return redirect()->route('tienda.index')->with('success', '¡Comprobante enviado exitosamente!');
    }
}