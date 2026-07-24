<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    /**
     * 1. Registrar un Pago (Hecho por el Cliente)
     */
    public function registrarPago(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pedido_id' => 'required|exists:pedidos,id',
            'metodo_pago' => 'required|string|max:50', // Ej: Nequi, Daviplata
            'comprobante_url' => 'nullable|string', // URL o referencia de la transferencia
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el pedido pertenezca al cliente logueado
        $pedido = Pedido::where('id', $request->pedido_id)
            ->where('usuario_id', $request->user()->id)
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado o no le pertenece.'
            ], 404);
        }

        // Crear el registro de pago en estado 'Pendiente'
        $pago = Pago::create([
            'pedido_id' => $request->pedido_id,
            'metodo_pago' => $request->metodo_pago,
            'comprobante_url' => $request->comprobante_url,
            'estado' => 'Pendiente'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comprobante de pago registrado. Esperando validación del administrador.',
            'data' => $pago
        ], 201);
    }

    /**
     * 2. Aprobar Pago y Registrar Venta (Solo Administrador)
     */
    public function validarPago(Request $request, $id)
    {
        // El usuario logueado debe ser Administrador (Rol ID = 1)
        if ($request->user()->rol_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Se requieren permisos de Administrador.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:Verificado,Rechazado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $pago = Pago::with('pedido')->find($id);

        if (!$pago) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de pago no encontrado.'
            ], 404);
        }

        if ($pago->estado === 'Verificado') {
            return response()->json([
                'success' => false,
                'message' => 'Este pago ya había sido verificado anteriormente.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Actualizar estado del pago
            $pago->update([
                'estado' => $request->estado
            ]);

            if ($request->estado === 'Verificado') {
                // Actualizar estado del pedido a Confirmado
                $pago->pedido->update([
                    'estado' => 'Confirmado'
                ]);

                // Generar automáticamente el registro en la tabla de Ventas
                Venta::create([
                    'pedido_id' => $pago->pedido_id,
                    'monto_total' => $pago->pedido->total,
                    'fecha_venta' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pago verificado con éxito. Venta registrada en contabilidad y pedido confirmado.',
                    'pago' => $pago->load('pedido')
                ], 200);
            }

            // Si es rechazado
            $pago->pedido->update([
                'estado' => 'Pago Rechazado'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El pago ha sido rechazado. El cliente deberá subir otro comprobante.',
                'pago' => $pago->load('pedido')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la validación del pago.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}