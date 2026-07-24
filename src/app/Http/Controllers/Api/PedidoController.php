<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{
    /**
     * Mostrar los pedidos del usuario autenticado (si es cliente) 
     * o todos los pedidos (si es administrador)
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        // Si es Administrador (Rol ID = 1), puede ver TODOS los pedidos del negocio
        if ($usuario->rol_id == 1) {
            $pedidos = Pedido::with(['usuario', 'detalles.producto', 'pago'])->get();
        } else {
            // Si es Cliente, solo ve sus propios pedidos
            $pedidos = Pedido::with(['detalles.producto', 'pago'])
                ->where('usuario_id', $usuario->id)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $pedidos
        ], 200);
    }

    /**
     * Crear un nuevo pedido (Solo para Clientes logueados)
     */
    public function store(Request $request)
    {
        // 1. Validar la estructura del pedido
        $validator = Validator::make($request->all(), [
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.talla' => 'required|string|max:10', // Talla del zapato
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Usamos una transacción de BD por seguridad
        DB::beginTransaction();

        try {
            // 2. Crear la cabecera del Pedido vacío inicialmente
            $pedido = Pedido::create([
                'usuario_id' => $request->user()->id,
                'estado' => 'Pendiente de pago',
                'total' => 0.00
            ]);

            $totalAcumulado = 0;

            // 3. Recorrer los productos solicitados para calcular el total e insertar los detalles
            foreach ($request->productos as $item) {
                // Buscamos el precio real en BD del zapato
                $producto = Producto::find($item['producto_id']);
                $subtotal = $producto->precio * $item['cantidad'];
                $totalAcumulado += $subtotal;

                // Crear el detalle del pedido
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'talla' => $item['talla'],
                    'precio_unitario' => $producto->precio
                ]);
            }

            // 4. Actualizar el total real calculado en el pedido
            $pedido->update([
                'total' => $totalAcumulado
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado con éxito. Por favor, suba el comprobante de pago.',
                'data' => $pedido->load('detalles.producto')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Deshace los cambios si algo falla
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}