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

        DB::table('ventas')->insert([
            'pedido_id'   => $pedido_id,
            'monto_total' => $pedido->total, 
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');
        $updateData = ['updated_at' => now()];
        if (in_array('estado', $columnasPedidos)) $updateData['estado'] = 'pagado';
        
        DB::table('pedidos')->where('id', $pedido_id)->update($updateData);
        DB::table('pagos')->where('pedido_id', $pedido_id)->delete();

        return redirect()->back()->with('success', '¡Pago verificado y venta asentada con éxito!');
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