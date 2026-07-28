<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PedidoService
{
    /**
     * Obtener pagos pendientes por revisar para la vista /admin/pagos
     */
    public function obtenerPagosPendientes(int $perPage = 15)
    {
        $pagos = DB::table('pedidos')
            ->leftJoin('users', 'pedidos.usuario_id', '=', 'users.id')
            ->leftJoin('ventas', 'ventas.pedido_id', '=', 'pedidos.id')
            ->join('pagos', 'pagos.pedido_id', '=', 'pedidos.id')
            ->whereNotNull('pagos.comprobante_url')
            ->whereNull('ventas.id')
            ->where(function ($q) {
                $q->whereNotIn('pedidos.estado', ['confirmado', 'aprobado', 'pagado', 'cancelado', 'rechazado'])
                  ->orWhereNull('pedidos.estado');
            })
            ->select(
                'pedidos.id as pedido_id',
                'pedidos.id as id',
                'pedidos.total as monto_total',
                'pedidos.total as total',
                'pedidos.created_at as fecha',
                'pedidos.estado as estado_pedido',
                'pedidos.direccion',
                'pedidos.ciudad',
                'pedidos.departamento',
                DB::raw("COALESCE(NULLIF(pedidos.barrio, ''), 'No especificado') as barrio"),
                DB::raw("COALESCE(NULLIF(pedidos.indicaciones, ''), 'Sin observaciones') as indicaciones"),
                DB::raw("COALESCE(NULLIF(pedidos.cedula, ''), 'No especificada') as cedula"),
                DB::raw("COALESCE(NULLIF(pedidos.telefono, ''), users.telefono, 'Sin teléfono') as telefono_final"),
                'users.nombre',
                'users.apellido',
                'users.email',
                'users.nombre as user_nombre',
                'users.apellido as user_apellido',
                'users.email as user_email',
                'ventas.id as venta_id',
                'pagos.comprobante_url as comprobante'
            )
            ->orderBy('pedidos.id', 'desc')
            ->paginate($perPage);

        foreach ($pagos as $pago) {
            $pago->detalles = DB::table('detalle_pedido')
                ->leftJoin('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
                ->where('detalle_pedido.pedido_id', $pago->pedido_id)
                ->select(
                    'detalle_pedido.*',
                    'productos.nombre as producto_nombre',
                    'productos.imagen_url as producto_imagen'
                )
                ->get();
        }

        return $pagos;
    }

    /**
     * Obtener pedidos filtrados y conteos para la vista /admin/pedidos
     */
    public function obtenerPedidosConFiltro(string $filtro = 'todos', int $perPage = 15): array
    {
        $query = DB::table('pedidos')
            ->leftJoin('users', 'pedidos.usuario_id', '=', 'users.id')
            ->leftJoin('ventas', 'ventas.pedido_id', '=', 'pedidos.id')
            ->leftJoin('pagos', 'pagos.pedido_id', '=', 'pedidos.id');

        if ($filtro === 'confirmados') {
            $query->where(function ($q) {
                $q->whereIn('pedidos.estado', ['pagado', 'confirmado', 'aprobado'])
                  ->orWhereNotNull('ventas.id');
            });
        } elseif ($filtro === 'pendientes') {
            $query->whereNull('ventas.id')
                  ->where(function ($q) {
                      $q->whereNotIn('pedidos.estado', ['cancelado', 'rechazado'])
                        ->orWhereNull('pedidos.estado');
                  });
        } elseif ($filtro === 'cancelados') {
            $query->whereIn('pedidos.estado', ['cancelado', 'rechazado']);
        }

        $pedidos = $query->select(
                'pedidos.id as pedido_id',
                'pedidos.id as id',
                'pedidos.total as monto_total',
                'pedidos.total as total',
                'pedidos.created_at as fecha',
                'pedidos.estado as estado_pedido',
                'pedidos.direccion',
                'pedidos.ciudad',
                'pedidos.departamento',
                DB::raw("COALESCE(NULLIF(pedidos.barrio, ''), 'No especificado') as barrio"),
                DB::raw("COALESCE(NULLIF(pedidos.indicaciones, ''), 'Sin observaciones') as indicaciones"),
                DB::raw("COALESCE(NULLIF(pedidos.cedula, ''), 'No especificada') as cedula"),
                DB::raw("COALESCE(NULLIF(pedidos.telefono, ''), users.telefono, 'Sin teléfono') as telefono_final"),
                'users.nombre',
                'users.apellido',
                'users.email',
                'users.nombre as user_nombre',
                'users.apellido as user_apellido',
                'users.email as user_email',
                'ventas.id as venta_id',
                'pagos.comprobante_url as comprobante'
            )
            ->orderBy('pedidos.id', 'desc')
            ->paginate($perPage);

        foreach ($pedidos as $pedido) {
            $pedido->detalles = DB::table('detalle_pedido')
                ->leftJoin('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
                ->where('detalle_pedido.pedido_id', $pedido->pedido_id)
                ->select(
                    'detalle_pedido.*',
                    'productos.nombre as producto_nombre',
                    'productos.imagen_url as producto_imagen'
                )
                ->get();
        }

        $conteos = [
            'todos'       => DB::table('pedidos')->count(),
            'confirmados' => DB::table('pedidos')
                ->leftJoin('ventas', 'ventas.pedido_id', '=', 'pedidos.id')
                ->where(function ($q) {
                    $q->whereIn('pedidos.estado', ['pagado', 'confirmado', 'aprobado'])
                      ->orWhereNotNull('ventas.id');
                })
                ->count(),
            'pendientes'  => DB::table('pedidos')
                ->leftJoin('ventas', 'ventas.pedido_id', '=', 'pedidos.id')
                ->whereNull('ventas.id')
                ->where(function ($q) {
                    $q->whereNotIn('pedidos.estado', ['cancelado', 'rechazado'])
                      ->orWhereNull('pedidos.estado');
                })
                ->count(),
            'cancelados'  => DB::table('pedidos')
                ->whereIn('estado', ['cancelado', 'rechazado'])
                ->count(),
        ];

        return [
            'pedidos' => $pedidos,
            'conteos' => $conteos,
        ];
    }

    /**
     * Lógica para aprobar un pedido y registrar la venta
     */
    public function aprobarPedido(int $pedidoId): array
    {
        $pedido = DB::table('pedidos')->where('id', $pedidoId)->first();
        if (!$pedido) {
            return ['status' => 'error', 'message' => 'El pedido no existe.'];
        }

        $yaExisteVenta = DB::table('ventas')->where('pedido_id', $pedidoId)->exists();
        if ($yaExisteVenta) {
            return ['status' => 'info', 'message' => 'Este pedido ya fue aprobado previamente.'];
        }

        DB::transaction(function () use ($pedidoId, $pedido) {
            DB::table('ventas')->insert([
                'pedido_id'   => $pedidoId,
                'monto_total' => $pedido->total,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            DB::table('pedidos')->where('id', $pedidoId)->update([
                'estado'     => 'confirmado',
                'updated_at' => now()
            ]);
        });

        return ['status' => 'success', 'message' => '¡Pago aprobado y pedido registrado en ventas!'];
    }

    /**
     * Lógica para rechazar un pedido y restaurar el stock exacto por talla
     */
    public function rechazarPedido(int $pedidoId): array
    {
        $pedido = DB::table('pedidos')->where('id', $pedidoId)->first();
        if (!$pedido) {
            return ['status' => 'error', 'message' => 'El pedido no existe.'];
        }

        if (in_array($pedido->estado, ['cancelado', 'rechazado'])) {
            return ['status' => 'info', 'message' => 'Este pedido ya fue rechazado anteriormente.'];
        }

        DB::transaction(function () use ($pedidoId) {
            $detalles = DB::table('detalle_pedido')->where('pedido_id', $pedidoId)->get();

            foreach ($detalles as $item) {
                $productoId = $item->producto_id ?? null;
                $cantidad   = (int) ($item->cantidad ?? $item->cant ?? 1);

                if (!$productoId) {
                    continue;
                }

                $tallaId = null;

                if (!empty($item->talla_id)) {
                    $tallaId = $item->talla_id;
                } else {
                    $valorTalla = trim((string)($item->talla ?? $item->numero ?? ''));
                    $soloNumero = preg_replace('/[^0-9.]/', '', $valorTalla);

                    $tallaRow = DB::table('tallas')
                        ->where('numero', $valorTalla)
                        ->orWhere('numero', $soloNumero)
                        ->first();

                    if ($tallaRow) {
                        $tallaId = $tallaRow->id;
                    } elseif (is_numeric($soloNumero)) {
                        $tallaId = $soloNumero;
                    }
                }

                if ($tallaId) {
                    DB::table('producto_talla')
                        ->where('producto_id', $productoId)
                        ->where('talla_id', $tallaId)
                        ->increment('stock', $cantidad);
                }
            }

            DB::table('pedidos')->where('id', $pedidoId)->update([
                'estado'     => 'cancelado',
                'updated_at' => now()
            ]);

            DB::table('ventas')->where('pedido_id', $pedidoId)->delete();
        });

        return ['status' => 'success', 'message' => 'El pedido fue rechazado y el stock fue devuelto correctamente.'];
    }
}