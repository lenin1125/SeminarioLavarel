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

        // Transacción SQL para garantizar consistencia total
        $pedidoId = DB::transaction(function () use ($request, $carrito) {
            // 1. Calcular total de la compra de forma segura
            $total = 0;
            foreach ($carrito as $item) {
                $itemData = (array)$item;
                $precio   = $itemData['precio'] ?? $itemData['precio_unitario'] ?? $itemData['valor'] ?? 0;
                $cantidad = $itemData['cantidad'] ?? $itemData['cant'] ?? $itemData['qty'] ?? 1;
                $total   += ((float)$precio * (int)$cantidad);
            }

            $user = Auth::user();
            $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');

            $datosPedido = [
                'usuario_id' => Auth::id(),
                'total'      => $total,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (in_array('cedula', $columnasPedidos)) $datosPedido['cedula'] = $request->input('cedula');
            if (in_array('telefono', $columnasPedidos)) $datosPedido['telefono'] = $request->input('telefono', $user->telefono ?? null);
            if (in_array('direccion', $columnasPedidos)) $datosPedido['direccion'] = $request->input('direccion');
            if (in_array('barrio', $columnasPedidos)) $datosPedido['barrio'] = $request->input('barrio');
            if (in_array('indicaciones', $columnasPedidos)) $datosPedido['indicaciones'] = $request->input('indicaciones');
            if (in_array('ciudad', $columnasPedidos)) $datosPedido['ciudad'] = $request->input('ciudad');
            if (in_array('departamento', $columnasPedidos)) $datosPedido['departamento'] = $request->input('departamento');

            $idPedido = DB::table('pedidos')->insertGetId($datosPedido);

            // 2. Detectar nombre exacto de la tabla de detalles
            $tablaDetalle = 'detalle_pedido';
            if (!DB::getSchemaBuilder()->hasTable('detalle_pedido')) {
                if (DB::getSchemaBuilder()->hasTable('detalle_pedidos')) {
                    $tablaDetalle = 'detalle_pedidos';
                } elseif (DB::getSchemaBuilder()->hasTable('pedido_detalles')) {
                    $tablaDetalle = 'pedido_detalles';
                }
            }

            $columnasDetalle = DB::getSchemaBuilder()->getColumnListing($tablaDetalle);

            // Detectar nombres exactos de columnas disponibles
            $colProductoId = in_array('producto_id', $columnasDetalle) ? 'producto_id' : (in_array('zapato_id', $columnasDetalle) ? 'zapato_id' : 'producto_id');
            $colCantidad   = in_array('cantidad', $columnasDetalle) ? 'cantidad' : (in_array('cant', $columnasDetalle) ? 'cant' : 'cantidad');
            $colTalla      = in_array('talla', $columnasDetalle) ? 'talla' : (in_array('numero', $columnasDetalle) ? 'numero' : 'talla');
            $colPrecio     = in_array('precio_unitario', $columnasDetalle) ? 'precio_unitario' : (in_array('precio', $columnasDetalle) ? 'precio' : 'precio_unitario');

            // 3. Guardar cada ítem en el desglose de pedido
            foreach ($carrito as $key => $item) {
                $itemData = (array)$item;

                // Extraer ID del producto probando múltiples llaves y fallback regex
                $productoId = $itemData['id'] ?? $itemData['producto_id'] ?? $itemData['zapato_id'] ?? $itemData['item_id'] ?? null;
                if (!$productoId) {
                    preg_match('/\d+/', (string)$key, $matches);
                    $productoId = $matches[0] ?? null;
                }

                $cantidad = $itemData['cantidad'] ?? $itemData['cant'] ?? $itemData['qty'] ?? 1;
                $talla    = $itemData['talla'] ?? $itemData['numero'] ?? $itemData['size'] ?? 'N/A';
                $precio   = $itemData['precio'] ?? $itemData['precio_unitario'] ?? $itemData['valor'] ?? 0;

                $insertDetalle = [
                    'pedido_id'    => $idPedido,
                    $colProductoId => $productoId,
                    $colCantidad   => $cantidad,
                    $colTalla      => $talla,
                    $colPrecio     => $precio,
                ];

                if (in_array('created_at', $columnasDetalle)) $insertDetalle['created_at'] = now();
                if (in_array('updated_at', $columnasDetalle)) $insertDetalle['updated_at'] = now();

                DB::table($tablaDetalle)->insert($insertDetalle);
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