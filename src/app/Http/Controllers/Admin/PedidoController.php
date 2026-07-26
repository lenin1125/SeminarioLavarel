<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function pagosIndex()
    {
        $columnasPagos = DB::getSchemaBuilder()->getColumnListing('pagos');
        $columnaImagen = 'comprobante'; 
        if (in_array('comprobante_pago', $columnasPagos)) $columnaImagen = 'comprobante_pago';
        elseif (in_array('imagen', $columnasPagos)) $columnaImagen = 'imagen';
        elseif (in_array('url', $columnasPagos)) $columnaImagen = 'url';
        elseif (in_array('comprobante_url', $columnasPagos)) $columnaImagen = 'comprobante_url';

        $pagosPorVerificar = DB::table('pagos')
            ->join('pedidos', 'pagos.pedido_id', '=', 'pedidos.id')
            ->join('users', 'pedidos.usuario_id', '=', 'users.id')
            ->select(
                'pagos.id as pago_id', 
                'pagos.pedido_id', 
                "pagos.{$columnaImagen} as comprobante", 
                'pedidos.total', 
                'users.nombre', 
                'users.apellido'
            )
            ->orderBy('pagos.id', 'asc')
            ->get();
            
        return view('admin.pagos.index', compact('pagosPorVerificar'));
    }

    public function aprobar($pedido_id)
    {
        $pedido = DB::table('pedidos')->where('id', $pedido_id)->first();
        if (!$pedido) return redirect()->back()->with('error', 'El pedido no existe.');

        DB::transaction(function () use ($pedido_id, $pedido) {
            // 1. Asentar la venta
            DB::table('ventas')->insert([
                'pedido_id'   => $pedido_id,
                'monto_total' => $pedido->total, 
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            // 2. Actualizar estado del pedido a 'pagado'
            $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');
            $updateData = ['updated_at' => now()];
            if (in_array('estado', $columnasPedidos)) $updateData['estado'] = 'pagado';
            
            DB::table('pedidos')->where('id', $pedido_id)->update($updateData);

            // 3. Eliminar comprobante temporal de la tabla pagos
            DB::table('pagos')->where('pedido_id', $pedido_id)->delete();

            // 4. DESCONTAR STOCK REAL Y DESACTIVAR SI SE AGOTA
            $detalles = DB::table('detalle_pedido')->where('pedido_id', $pedido_id)->get();

            if (DB::getSchemaBuilder()->hasTable('producto_talla')) {
                $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
                $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

                foreach ($detalles as $item) {
                    $productoId = $item->producto_id;
                    $cantidadComprada = (int) $item->cantidad;
                    $tallaComprada = (string) $item->talla;

                    $queryPivote = DB::table('producto_talla')->where('producto_id', $productoId);

                    // A) Filtrar por talla
                    if (in_array('talla_id', $columnasPivote)) {
                        $tallaId = null;
                        if (DB::getSchemaBuilder()->hasTable('tallas')) {
                            $columnasTallas = DB::getSchemaBuilder()->getColumnListing('tallas');
                            $tallaQuery = DB::table('tallas');

                            if (in_array('talla', $columnasTallas)) {
                                $tallaQuery->where('talla', $tallaComprada);
                            } elseif (in_array('numero', $columnasTallas)) {
                                $tallaQuery->where('numero', $tallaComprada);
                            } elseif (in_array('nombre', $columnasTallas)) {
                                $tallaQuery->where('nombre', $tallaComprada);
                            } else {
                                $tallaQuery->where('id', $tallaComprada);
                            }

                            $tallaObj = $tallaQuery->first();
                            if ($tallaObj) {
                                $tallaId = $tallaObj->id;
                            }
                        }

                        if ($tallaId) {
                            $queryPivote->where('talla_id', $tallaId);
                        } else {
                            $queryPivote->where('talla_id', $tallaComprada);
                        }
                    } elseif (in_array('talla', $columnasPivote)) {
                        $queryPivote->where('talla', $tallaComprada);
                    }

                    // Decrementar stock de la talla seleccionada
                    $queryPivote->decrement($columnaStock, $cantidadComprada);

                    // Decrementar stock general si existe en la tabla productos
                    if (DB::getSchemaBuilder()->hasColumn('productos', 'stock')) {
                        DB::table('productos')->where('id', $productoId)->decrement('stock', $cantidadComprada);
                    } elseif (DB::getSchemaBuilder()->hasColumn('productos', 'cantidad')) {
                        DB::table('productos')->where('id', $productoId)->decrement('cantidad', $cantidadComprada);
                    }

                    // B) AUTOMATIZACIÓN: Verificar si el producto se quedó sin inventario total
                    $stockRestanteTallas = DB::table('producto_talla')
                        ->where('producto_id', $productoId)
                        ->sum($columnaStock);

                    if ($stockRestanteTallas <= 0) {
                        $updateCampos = [];
                        
                        if (DB::getSchemaBuilder()->hasColumn('productos', 'activo')) {
                            $updateCampos['activo'] = 0; // Desactiva el producto automáticamente
                        }
                        if (DB::getSchemaBuilder()->hasColumn('productos', 'estado')) {
                            $updateCampos['estado'] = 'Agotado'; // Cambia el estado si existe esa columna
                        }

                        if (!empty($updateCampos)) {
                            DB::table('productos')->where('id', $productoId)->update($updateCampos);
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', '¡Pago verificado, venta asentada y stock actualizado!');
    }

    public function rechazar($id)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pedidos')->where('id', $id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'El pedido y su comprobante fueron rechazados.');
    }

    public function confirmadosIndex()
    {
        $pedidosConfirmados = DB::table('ventas')
            ->join('pedidos', 'ventas.pedido_id', '=', 'pedidos.id')
            ->leftJoin('users', 'pedidos.usuario_id', '=', 'users.id')
            ->select(
                'ventas.id as consecutivo_confirmado',
                'pedidos.id as pedido_id',
                'users.nombre as user_nombre',
                'users.apellido as user_apellido',
                'users.email as user_email',
                DB::raw("COALESCE(NULLIF(pedidos.telefono, ''), users.telefono) as telefono_final"),
                'pedidos.direccion',
                'pedidos.ciudad',
                'pedidos.departamento',
                'ventas.monto_total',
                'ventas.created_at as fecha_confirmacion'
            )
            ->orderBy('ventas.id', 'desc')
            ->paginate(10);

        return view('admin.pedidos_confirmados', compact('pedidosConfirmados'));
    }
}