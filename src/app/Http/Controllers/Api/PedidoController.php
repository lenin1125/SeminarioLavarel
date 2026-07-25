<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{
    /**
     * Auxiliar para detectar la columna de stock en la tabla pivote ('stock' o 'cantidad')
     */
    private function getColumnaStock()
    {
        $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
        return in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);
    }

    /**
     * Mostrar los pedidos del usuario autenticado (si es cliente) 
     * o todos los pedidos (si es administrador)
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        // Si es Administrador (Rol ID = 1), puede ver TODOS los pedidos
        if ((int)$usuario->rol_id === 1) {
            $pedidos = Pedido::with(['usuario', 'detalles.producto.categoria', 'pago'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Si es Cliente, solo ve sus propios pedidos
            $pedidos = Pedido::with(['detalles.producto.categoria', 'pago'])
                ->where('usuario_id', $usuario->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data'    => $pedidos
        ], 200);
    }

    /**
     * Crear un nuevo pedido (Solo para Clientes logueados)
     */
    public function store(Request $request)
    {
        // 1. Validar la estructura del pedido
        $validator = Validator::make($request->all(), [
            'productos'               => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad'    => 'required|integer|min:1',
            'productos.*.talla'       => 'required|string|max:10', // Puede ser ID de talla o número (ej: "39")
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $totalAcumulado = 0;
            $detallesParaCrear = [];
            $columnaStock = $this->getColumnaStock();

            // 2. Validar disponibilidad, activo y calcular totales antes de guardar
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);

                // Validar que el producto esté activo
                if (!$producto || !$producto->activo) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "El producto '{$producto->nombre}' no está disponible actualmente."
                    ], 400);
                }

                // Resolver ID o Nombre de la Talla
                $tallaVal = $item['talla'];
                $tallaModel = is_numeric($tallaVal) 
                    ? Talla::find($tallaVal) ?? Talla::where('talla', (string)$tallaVal)->first()
                    : Talla::where('talla', (string)$tallaVal)->first();

                if ($tallaModel && $columnaStock) {
                    $registroPivote = DB::table('producto_talla')
                        ->where('producto_id', $producto->id)
                        ->where('talla_id', $tallaModel->id)
                        ->first();

                    $stockDisponible = $registroPivote ? ($registroPivote->{$columnaStock} ?? 0) : 0;

                    if ($stockDisponible < $item['cantidad']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stock insuficiente para el producto '{$producto->nombre}' en la talla {$tallaVal}. Disponible: {$stockDisponible}."
                        ], 400);
                    }

                    // Descontar Stock de la Talla
                    DB::table('producto_talla')
                        ->where('producto_id', $producto->id)
                        ->where('talla_id', $tallaModel->id)
                        ->decrement($columnaStock, $item['cantidad']);
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $totalAcumulado += $subtotal;

                $detallesParaCrear[] = [
                    'producto_id'     => $producto->id,
                    'cantidad'        => $item['cantidad'],
                    'talla'           => (string)$tallaVal,
                    'precio_unitario' => $producto->precio
                ];
            }

            // 3. Crear Cabecera del Pedido
            $pedido = Pedido::create([
                'usuario_id' => $request->user()->id,
                'estado'     => 'Pendiente de pago',
                'total'      => $totalAcumulado
            ]);

            // 4. Insertar los detalles del pedido
            foreach ($detallesParaCrear as $detalle) {
                $detalle['pedido_id'] = $pedido->id;
                DetallePedido::create($detalle);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado con éxito. Por favor, realiza el pago y suba el comprobante.',
                'data'    => $pedido->load('detalles.producto')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}