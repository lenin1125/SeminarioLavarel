@extends('layouts.admin')

@section('content')
<div class="p-8">
    <!-- Encabezado de la Sección -->
    <div class="mb-6 border-b border-gray-800 pb-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white uppercase">Gestión de Pedidos</h1>
            <p class="text-gray-400 text-sm mt-1">Historial unificado y consulta de órdenes por estado.</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/30 text-indigo-400 font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2">
            <span>Total en Lista:</span>
            <span class="text-white font-black">{{ $pedidos->total() }}</span>
        </div>
    </div>

    <!-- SECCIÓN: EXPORTAR REPORTE PDF POR RANGO DE FECHAS -->
    <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl mb-6 shadow-xl">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg">📄</span>
            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Exportar Reporte de Pedidos (PDF)</h3>
        </div>

        <form method="GET" action="{{ route('admin.pedidos.exportar_pdf') }}" class="flex flex-col md:flex-row items-end gap-4">
            <!-- Fecha Inicio -->
            <div class="w-full md:w-auto flex-1">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                    Fecha Inicio
                </label>
                <input type="date" 
                       name="fecha_inicio" 
                       required 
                       value="{{ date('Y-m-01') }}" 
                       class="w-full bg-gray-950 border border-gray-800 text-white text-xs rounded-xl px-3 py-2.5 focus:border-indigo-500 focus:outline-none">
            </div>

            <!-- Fecha Fin -->
            <div class="w-full md:w-auto flex-1">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                    Fecha Fin
                </label>
                <input type="date" 
                       name="fecha_fin" 
                       required 
                       value="{{ date('Y-m-d') }}" 
                       class="w-full bg-gray-950 border border-gray-800 text-white text-xs rounded-xl px-3 py-2.5 focus:border-indigo-500 focus:outline-none">
            </div>

            <!-- Botón de Exportar -->
            <button type="submit" 
                    class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>📥</span> Generar Reporte PDF
            </button>
        </form>
    </div>

    <!-- BARRA DE FILTROS (PESTAÑAS) -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.pedidos.index', ['estado' => 'todos']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filtro === 'todos' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-white border border-gray-800' }}">
            <span>📋 Todos</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filtro === 'todos' ? 'bg-indigo-800 text-white' : 'bg-gray-800 text-gray-400' }}">{{ $conteos['todos'] }}</span>
        </a>

        <a href="{{ route('admin.pedidos.index', ['estado' => 'confirmados']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filtro === 'confirmados' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-emerald-400 border border-gray-800' }}">
            <span>✅ Confirmados</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filtro === 'confirmados' ? 'bg-emerald-800 text-white' : 'bg-gray-800 text-gray-400' }}">{{ $conteos['confirmados'] }}</span>
        </a>

        <a href="{{ route('admin.pedidos.index', ['estado' => 'pendientes']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filtro === 'pendientes' ? 'bg-amber-600 text-white shadow-lg' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-amber-400 border border-gray-800' }}">
            <span>⏳ Pendientes</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filtro === 'pendientes' ? 'bg-amber-800 text-white' : 'bg-gray-800 text-gray-400' }}">{{ $conteos['pendientes'] }}</span>
        </a>

        <a href="{{ route('admin.pedidos.index', ['estado' => 'cancelados']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filtro === 'cancelados' ? 'bg-rose-600 text-white shadow-lg' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-rose-400 border border-gray-800' }}">
            <span>❌ Cancelados</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filtro === 'cancelados' ? 'bg-rose-800 text-white' : 'bg-gray-800 text-gray-400' }}">{{ $conteos['cancelados'] }}</span>
        </a>
    </div>

    <!-- TABLA DE PEDIDOS -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950 border-b border-gray-800 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="p-4 whitespace-nowrap">N° Pedido</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Contacto / WhatsApp</th>
                        <th class="p-4 text-center">Información de Venta</th>
                        <th class="p-4">Monto Total</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($pedidos as $pedido)
                        @php
                            $tieneUsuarioActivo = !empty($pedido->user_nombre);
                            $nombreCliente = $tieneUsuarioActivo ? trim($pedido->user_nombre . ' ' . $pedido->user_apellido) : null;
                            $esConfirmado = $pedido->venta_id || in_array($pedido->estado_pedido, ['confirmado', 'pagado', 'aprobado']);
                            $esCancelado  = in_array($pedido->estado_pedido, ['cancelado', 'rechazado']);
                        @endphp
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            
                            <!-- Consecutivo/ID de Pedido -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-block font-black text-indigo-400 bg-indigo-500/10 border border-indigo-500/30 px-3 py-1.5 rounded-lg text-xs tracking-wider">
                                    #PED-{{ str_pad($pedido->pedido_id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Nombre del Cliente -->
                            <td class="p-4 font-bold text-white whitespace-nowrap">
                                @if($tieneUsuarioActivo)
                                    {{ $nombreCliente }}
                                @else
                                    <div class="flex flex-col">
                                        <span class="text-amber-400 text-xs font-bold">⚠️ Cliente de Venta Histórica</span>
                                        <span class="text-[10px] text-gray-500 font-normal">(Cuenta eliminada)</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Contacto / WhatsApp -->
                            <td class="p-4">
                                <div class="text-xs text-gray-300 font-medium mb-1">
                                    {{ $pedido->user_email ?? 'Sin correo' }}
                                </div>
                                @if(!empty($pedido->telefono_final) || !empty($pedido->telefono))
                                    @php $tel = !empty($pedido->telefono_final) ? $pedido->telefono_final : $pedido->telefono; @endphp
                                    <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $tel) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 text-xs font-bold hover:underline">
                                        💬 {{ $tel }}
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-500 italic">Sin WhatsApp</span>
                                @endif
                            </td>

                            <!-- Modales Datos de Envío y Productos -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" 
                                            onclick="openModal('modal-envio-{{ $pedido->pedido_id }}')" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                                        📍 Envío
                                    </button>

                                    <button type="button" 
                                            onclick="openModal('modal-productos-conf-{{ $pedido->pedido_id }}')" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                                        📦 Productos
                                    </button>
                                </div>
                            </td>

                            <!-- Monto -->
                            <td class="p-4 font-black text-emerald-400 whitespace-nowrap">
                                ${{ number_format($pedido->monto_total, 0, ',', '.') }} COP
                            </td>

                            <!-- Fecha -->
                            <td class="p-4 text-gray-300 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y - h:i A') }}
                            </td>

                            <!-- Estado Badge -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($esConfirmado)
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase">
                                        ✅ Confirmado
                                    </span>
                                @elseif($esCancelado)
                                    <span class="bg-rose-500/10 text-rose-400 border border-rose-500/30 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase">
                                        ❌ Cancelado
                                    </span>
                                @else
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase">
                                        ⏳ Pendiente
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if(!$esConfirmado && !$esCancelado)
                                    <a href="{{ url('/admin/pagos') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-xl text-xs font-bold transition shadow-sm">
                                        🔍 Validar Pago
                                    </a>
                                @else
                                    <span class="text-gray-600 text-xs italic">Sin acciones</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">📦</span>
                                <p class="font-semibold text-sm">No hay pedidos registrados en la categoría <strong class="text-indigo-400 uppercase">{{ $filtro }}</strong>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidos->hasPages())
            <div class="p-4 border-t border-gray-800 bg-gray-950/50">
                {{ $pedidos->appends(['estado' => $filtro])->links() }}
            </div>
        @endif
    </div>
</div>

<!-- INCLUSIÓN DE COMPONENTE MODALES -->
@include('admin.pedidos.partials.modales')

<!-- SCRIPT DIRECTO DE CONTROL DE MODALES -->
<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
        }
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
        }
    }
</script>

@endsection