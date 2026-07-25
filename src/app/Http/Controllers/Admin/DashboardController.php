<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function reportes()
    {
        $ventasDiarias = DB::table('ventas')
            ->whereDate('created_at', now()->toDateString())
            ->sum('monto_total');

        $ventasMensuales = DB::table('ventas')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto_total');

        $productoMasVendido = DB::table('detalle_pedido')
            ->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_pedido.pedido_id', '=', 'ventas.pedido_id')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->first();

        return view('admin.reportes', compact('ventasDiarias', 'ventasMensuales', 'productoMasVendido'));
    }

    public function proveedoresIndex()
    {
        $proveedores = DB::table('proveedores')->get();
        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function proveedoresStore(Request $request)
    {
        DB::table('proveedores')->insert([
            'nombre'     => $request->nombre,
            'telefono'   => $request->telefono,
            'ciudad'     => $request->ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Proveedor registrado.');
    }

    public function usuariosIndex()
    {
        $usuarios = DB::table('users')
            ->leftJoin('pedidos', 'users.id', '=', 'pedidos.usuario_id')
            ->select(
                'users.id',
                'users.nombre',
                'users.apellido',
                'users.email',
                'users.created_at',
                DB::raw('MAX(pedidos.telefono) as telefono')
            )
            ->groupBy('users.id', 'users.nombre', 'users.apellido', 'users.email', 'users.created_at')
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        return view('admin.usuarios', compact('usuarios'));
    }

    public function usuariosDestroy($id)
    {
        if ((int)$id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar la cuenta de administrador activa.');
        }

        DB::table('users')->where('id', $id)->delete();
        return back()->with('success', 'El usuario ha sido eliminado del sistema.');
    }
}