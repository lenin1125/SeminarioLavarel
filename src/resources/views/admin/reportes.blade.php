@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="border-b border-gray-800 pb-5">
        <h1 class="text-3xl font-black tracking-tight text-white uppercase">Panel de Estadísticas Contables y Rendimiento</h1>
        <p class="text-gray-400 text-xs mt-1">Métricas en tiempo real obtenidas de las compras validadas en el sistema.</p>
    </div>

    <!-- TARJETAS SUPERIORES -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Ventas de Hoy -->
        <div class="bg-gray-900 border-l-4 border-l-blue-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">Ventas de Hoy</span>
            <div class="text-3xl font-black text-emerald-400">${{ number_format($ventasDiarias ?? 0, 0, ',', '.') }} COP</div>
        </div>

        <!-- Ventas del Mes -->
        <div class="bg-gray-900 border-l-4 border-l-emerald-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">Ventas del Mes</span>
            <div class="text-3xl font-black text-emerald-400">${{ number_format($ventasMensuales ?? 0, 0, ',', '.') }} COP</div>
        </div>

        <!-- Producto Más Vendido -->
        <div class="bg-gray-900 border-l-4 border-l-amber-500 border-gray-800 border-y border-r p-6 rounded-2xl shadow-xl">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider block mb-2">🔥 Producto Más Vendido</span>
            @if(isset($productoMasVendido) && $productoMasVendido)
                <div class="text-xl font-black text-white truncate">{{ $productoMasVendido->nombre }}</div>
                <div class="text-xs font-bold text-amber-400 mt-1">({{ $productoMasVendido->total_vendido }} unidades entregadas)</div>
            @else
                <div class="text-sm font-semibold text-gray-500">Sin ventas registradas aún</div>
            @endif
        </div>

    </div>

    <!-- SECCIÓN DE GRÁFICOS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
        
        <!-- 📊 GRÁFICO 1: Historial de Ventas (Barras) -->
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl lg:col-span-2 flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                    <span>📊</span> Rendimiento e Historial de Ventas
                </h3>
                <p class="text-gray-400 text-xs mt-0.5">Ingresos acumulados por período.</p>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartBarrasVentas"></canvas>
            </div>
        </div>

        <!-- 🍩 GRÁFICO 2: Productos Más Vendidos (Dona) -->
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                    <span>🍩</span> Participación por Producto
                </h3>
                <p class="text-gray-400 text-xs mt-0.5">Distribución de unidades vendidas.</p>
            </div>
            <div class="relative w-full flex justify-center items-center" style="height: 280px;">
                <canvas id="chartDonaProductos"></canvas>
            </div>
        </div>

    </div>
</div>

<!-- Librería Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. CONFIGURACIÓN DEL GRÁFICO DE BARRAS
    const ctxBarras = document.getElementById('chartBarrasVentas').getContext('2d');
    new Chart(ctxBarras, {
        type: 'bar',
        data: {
            labels: {!! json_encode(isset($ventasPorMes) ? $ventasPorMes->pluck('mes_nombre') : []) !!},
            datasets: [{
                label: 'Ventas Totales ($)',
                data: {!! json_encode(isset($ventasPorMes) ? $ventasPorMes->pluck('total') : []) !!},
                backgroundColor: '#10b981',
                hoverBackgroundColor: '#059669',
                borderRadius: 8,
                barThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    ticks: { color: '#9ca3af', font: { size: 11, weight: 'bold' } },
                    grid: { display: false }
                },
                y: {
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: function(value) {
                            return '$' + new Intl.NumberFormat('es-CO').format(value);
                        }
                    },
                    grid: { color: '#1f2937' }
                }
            }
        }
    });

    // 2. CONFIGURACIÓN DEL GRÁFICO DE DONA
    const ctxDona = document.getElementById('chartDonaProductos').getContext('2d');
    new Chart(ctxDona, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(isset($topProductos) ? $topProductos->pluck('nombre') : []) !!},
            datasets: [{
                data: {!! json_encode(isset($topProductos) ? $topProductos->pluck('total_unidades') : []) !!},
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ec4899'
                ],
                borderWidth: 4,
                borderColor: '#111827'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#d1d5db', font: { size: 11, weight: 'bold' }, padding: 16 }
                }
            }
        }
    });

});
</script>
@endsection