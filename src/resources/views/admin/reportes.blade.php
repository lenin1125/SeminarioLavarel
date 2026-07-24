@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="border-b border-gray-800 pb-5">
        <h1 class="text-3xl font-black tracking-tight text-white uppercase">Panel de Estadísticas Contables y Rendimiento</h1>
        <p class="text-gray-400 text-xs mt-1">Métricas en tiempo real obtenidas de las compras validadas en el sistema.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Ventas de Hoy -->
        <div class="bg-gray-900 border-l-4 border-l-blue-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">Ventas de Hoy</span>
            <div class="text-3xl font-black text-emerald-400">${{ number_format($ventasDiarias, 0, ',', '.') }} COP</div>
        </div>

        <!-- Ventas del Mes -->
        <div class="bg-gray-900 border-l-4 border-l-emerald-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">Ventas del Mes</span>
            <div class="text-3xl font-black text-emerald-400">${{ number_format($ventasMensuales, 0, ',', '.') }} COP</div>
        </div>

        <!-- Producto Más Vendido -->
        <div class="bg-gray-900 border-l-4 border-l-amber-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">🔥 Producto Más Vendido</span>
            @if($productoMasVendido)
                <div class="text-xl font-black text-white truncate">{{ $productoMasVendido->nombre }}</div>
                <div class="text-xs font-bold text-amber-400 mt-1">({{ $productoMasVendido->total_vendido }} unidades entregadas)</div>
            @else
                <div class="text-sm font-semibold text-gray-500">Sin ventas registradas aún</div>
            @endif
        </div>

    </div>
</div>
@endsection