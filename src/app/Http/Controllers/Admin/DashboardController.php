<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function reportes()
    {
        // 1. Ventas acumuladas de HOY en la tabla pedidos
        $ventasDiarias = DB::table('pedidos')
            ->whereDate('created_at', now())
            ->sum('total');

        // 2. Ventas acumuladas de ESTE MES en la tabla pedidos
        $ventasMensuales = DB::table('pedidos')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // 3. Producto más vendido (Desde detalle_pedido)
        $productoMasVendido = DB::table('detalle_pedido')
            ->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->first();

        // 4. Historial de Ventas por Mes (Para el gráfico)
        $ventasPorMes = DB::table('pedidos')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as mes_nombre"),
                DB::raw("SUM(total) as total"),
                DB::raw("MIN(created_at) as min_fecha")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
            ->orderBy('min_fecha', 'asc')
            ->limit(6)
            ->get();

        // 5. Top 5 Productos más vendidos
        $topProductos = DB::table('detalle_pedido')
            ->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_unidades'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_unidades')
            ->limit(5)
            ->get();

        return view('admin.reportes', compact(
            'ventasDiarias',
            'ventasMensuales',
            'productoMasVendido',
            'ventasPorMes',
            'topProductos'
        ));
    }

    public function index()
    {
        return $this->reportes();
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

}