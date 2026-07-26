<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function pagosIndex()
    {
        // 1. Detectar automáticamente la columna de comprobante en la tabla 'pagos'
        $columnasPagos = DB::getSchemaBuilder()->getColumnListing('pagos');
        $posiblesComprobantes = ['comprobante_pago', 'comprobante_url', 'comprobante', 'imagen', 'foto', 'url', 'archivo', 'recibo', 'path'];
        $columnaImagenEncontrada = null;

        foreach ($posiblesComprobantes as $col) {
            if (in_array($col, $columnasPagos)) {
                $columnaImagenEncontrada = $col;
                break;
            }
        }

        $selectComprobante = $columnaImagenEncontrada 
            ? "pagos.{$columnaImagenEncontrada} as comprobante" 
            : DB::raw("'' as comprobante");

        // 2. Detectar nombres de usuario en la tabla 'users'
        $columnasUsers = DB::getSchemaBuilder()->getColumnListing('users');
        $selectNombre = in_array('nombre', $columnasUsers) ? 'users.nombre' : (in_array('name', $columnasUsers) ? 'users.name as nombre' : DB::raw("'Cliente' as nombre"));
        $selectApellido = in_array('apellido', $columnasUsers) ? 'users.apellido' : DB::raw("'' as apellido");

        $pagosPorVerificar = DB::table('pagos')
            ->join('pedidos', 'pagos.pedido_id', '=', 'pedidos.id')
            ->join('users', 'pedidos.usuario_id', '=', 'users.id')
            ->select(
                'pagos.id as pago_id', 
                'pagos.pedido_id', 
                $selectComprobante, 
                'pedidos.total', 
                $selectNombre, 
                $selectApellido
            )
            ->orderBy('pagos.id', 'asc')
            ->get();

        // 3. Detectar si la tabla del catálogo es 'zapatos' o 'productos'
        $tablaCatalogo = DB::getSchemaBuilder()->hasTable('zapatos') ? 'zapatos' : 'productos';
        $columnasCatalogo = DB::getSchemaBuilder()->hasTable($tablaCatalogo) ? DB::getSchemaBuilder()->getColumnListing($tablaCatalogo) : [];
        
        $posiblesFotos = ['imagen', 'foto', 'imagen_url', 'url', 'path'];
        $columnaFotoEncontrada = null;

        foreach ($posiblesFotos as $col) {
            if (in_array($col, $columnasCatalogo)) {
                $columnaFotoEncontrada = $col;
                break;
            }
        }

        $selectFoto = $columnaFotoEncontrada 
            ? "{$tablaCatalogo}.{$columnaFotoEncontrada} as producto_imagen" 
            : DB::raw("'' as producto_imagen");

        // 4. Traer el detalle con productos, tallas y cantidades de cada pedido
        foreach ($pagosPorVerificar as $pago) {
            $pago->detalles = DB::table('detalle_pedido')
                ->leftJoin($tablaCatalogo, 'detalle_pedido.producto_id', '=', "{$tablaCatalogo}.id")
                ->where('detalle_pedido.pedido_id', $pago->pedido_id)
                ->select(
                    'detalle_pedido.*',
                    "{$tablaCatalogo}.nombre as producto_nombre",
                    $selectFoto
                )
                ->get();
        }

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

            // 2. Actualizar estado del pedido
            $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');
            $updateData = ['updated_at' => now()];
            if (in_array('estado', $columnasPedidos)) $updateData['estado'] = 'pagado';
            
            DB::table('pedidos')->where('id', $pedido_id)->update($updateData);

            // 3. Eliminar comprobante temporal
            DB::table('pagos')->where('pedido_id', $pedido_id)->delete();

            // 4. Descontar Stock
            $detalles = DB::table('detalle_pedido')->where('pedido_id', $pedido_id)->get();

            if (DB::getSchemaBuilder()->hasTable('producto_talla')) {
                $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
                $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

                foreach ($detalles as $item) {
                    $productoId = $item->producto_id;
                    $cantidadComprada = (int) $item->cantidad;
                    $tallaComprada = (string) $item->talla;

                    $queryPivote = DB::table('producto_talla')->where('producto_id', $productoId);

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
                            if ($tallaObj) $tallaId = $tallaObj->id;
                        }

                        if ($tallaId) {
                            $queryPivote->where('talla_id', $tallaId);
                        } else {
                            $queryPivote->where('talla_id', $tallaComprada);
                        }
                    } elseif (in_array('talla', $columnasPivote)) {
                        $queryPivote->where('talla', $tallaComprada);
                    }

                    $queryPivote->decrement($columnaStock, $cantidadComprada);

                    $tablaCatalogo = DB::getSchemaBuilder()->hasTable('zapatos') ? 'zapatos' : 'productos';
                    if (DB::getSchemaBuilder()->hasColumn($tablaCatalogo, 'stock')) {
                        DB::table($tablaCatalogo)->where('id', $productoId)->decrement('stock', $cantidadComprada);
                    } elseif (DB::getSchemaBuilder()->hasColumn($tablaCatalogo, 'cantidad')) {
                        DB::table($tablaCatalogo)->where('id', $productoId)->decrement('cantidad', $cantidadComprada);
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
        $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');
        $selectBarrio = in_array('barrio', $columnasPedidos) ? 'pedidos.barrio' : DB::raw("'' as barrio");
        $selectIndicaciones = in_array('indicaciones', $columnasPedidos) ? 'pedidos.indicaciones' : DB::raw("'' as indicaciones");
        $selectCedula = in_array('cedula', $columnasPedidos) ? 'pedidos.cedula' : DB::raw("'' as cedula");

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
                $selectBarrio,
                $selectIndicaciones,
                $selectCedula,
                'ventas.monto_total',
                'ventas.created_at as fecha_confirmacion'
            )
            ->orderBy('ventas.id', 'desc')
            ->paginate(10);

        $tablaCatalogo = DB::getSchemaBuilder()->hasTable('zapatos') ? 'zapatos' : 'productos';
        $columnasCatalogo = DB::getSchemaBuilder()->hasTable($tablaCatalogo) ? DB::getSchemaBuilder()->getColumnListing($tablaCatalogo) : [];
        
        $posiblesFotos = ['imagen', 'foto', 'imagen_url', 'url', 'path'];
        $columnaFotoEncontrada = null;
        foreach ($posiblesFotos as $col) {
            if (in_array($col, $columnasCatalogo)) {
                $columnaFotoEncontrada = $col;
                break;
            }
        }
        $selectFoto = $columnaFotoEncontrada 
            ? "{$tablaCatalogo}.{$columnaFotoEncontrada} as producto_imagen" 
            : DB::raw("'' as producto_imagen");

        foreach ($pedidosConfirmados as $pedido) {
            $pedido->detalles = DB::table('detalle_pedido')
                ->leftJoin($tablaCatalogo, 'detalle_pedido.producto_id', '=', "{$tablaCatalogo}.id")
                ->where('detalle_pedido.pedido_id', $pedido->pedido_id)
                ->select(
                    'detalle_pedido.*',
                    "{$tablaCatalogo}.nombre as producto_nombre",
                    $selectFoto
                )
                ->get();
        }

        return view('admin.pedidos_confirmados', compact('pedidosConfirmados'));
    }
}