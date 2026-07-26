@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-8 border-b border-gray-800 pb-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white uppercase">Pedidos Confirmados</h1>
            <p class="text-gray-400 text-sm mt-1">Historial inmutable de ventas aprobadas para registro contable.</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/30 text-indigo-400 font-bold px-4 py-2 rounded-xl text-xs">
            Total Confirmados: {{ $pedidosConfirmados->total() }}
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950 border-b border-gray-800 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="p-4 whitespace-nowrap">N° Venta</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Contacto / WhatsApp</th>
                        <th class="p-4 text-center">Información de Venta</th>
                        <th class="p-4">Monto Pagado</th>
                        <th class="p-4">Fecha Aprobación</th>
                        <th class="p-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($pedidosConfirmados as $index => $pedido)
                        @php
                            $numeroConsecutivo = $pedidosConfirmados->total() - (($pedidosConfirmados->currentPage() - 1) * $pedidosConfirmados->perPage()) - $loop->index;
                            
                            $tieneUsuarioActivo = !empty($pedido->user_nombre);
                            $nombreCliente = $tieneUsuarioActivo ? trim($pedido->user_nombre . ' ' . $pedido->user_apellido) : null;
                        @endphp
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            
                            <!-- Secuencia Consecutiva -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-block font-black text-indigo-400 bg-indigo-500/10 border border-indigo-500/30 px-3 py-1.5 rounded-lg text-xs tracking-wider">
                                    CONF-{{ str_pad($numeroConsecutivo, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Nombre del Cliente -->
                            <td class="p-4 font-bold text-white whitespace-nowrap">
                                @if($tieneUsuarioActivo)
                                    {{ $nombreCliente }}
                                @else
                                    <div class="flex flex-col">
                                        <span class="text-amber-400 text-xs font-bold">⚠️ Cliente de Venta Histórica</span>
                                        <span class="text-[10px] text-gray-500 font-normal">(Cuenta de usuario eliminada)</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Contacto / WhatsApp -->
                            <td class="p-4">
                                <div class="text-xs text-gray-300 font-medium mb-1">
                                    {{ $pedido->user_email ?? 'Sin correo' }}
                                </div>
                                @if(!empty($pedido->telefono_final))
                                    <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $pedido->telefono_final) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 text-xs font-bold hover:underline">
                                        💬 {{ $pedido->telefono_final }}
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-500 italic">Sin WhatsApp</span>
                                @endif
                            </td>

                            <!-- Botones Datos de Envío y Productos -->
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

                            <!-- Monto Pagado -->
                            <td class="p-4 font-black text-emerald-400 whitespace-nowrap">
                                ${{ number_format($pedido->monto_total, 0, ',', '.') }} COP
                            </td>

                            <!-- Fecha Aprobación -->
                            <td class="p-4 text-gray-300 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pedido->fecha_confirmacion)->format('d/m/Y - h:i A') }}
                            </td>

                            <!-- Estado -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase">
                                    ✅ Confirmado
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">📦</span>
                                <p class="font-semibold text-sm">No hay pedidos confirmados registrados aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidosConfirmados->hasPages())
            <div class="p-4 border-t border-gray-800 bg-gray-950/50">
                {{ $pedidosConfirmados->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODALES DE DATOS DE ENVÍO Y PRODUCTOS -->
@foreach($pedidosConfirmados as $index => $pedido)
    @php
        $numeroConsecutivoModal = $pedidosConfirmados->total() - (($pedidosConfirmados->currentPage() - 1) * $pedidosConfirmados->perPage()) - $loop->index;
        $tieneUsuarioActivoModal = !empty($pedido->user_nombre);
        $nombreClienteModal = $tieneUsuarioActivoModal ? trim($pedido->user_nombre . ' ' . $pedido->user_apellido) : 'Cliente No Registrado';
    @endphp

    <!-- MODAL 1: DATOS DE ENVÍO -->
    <div id="modal-envio-{{ $pedido->pedido_id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full m-4 overflow-hidden shadow-2xl">
            <div class="p-5 bg-gray-950 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                    📍 Ficha de Envío — Pedido #{{ $pedido->pedido_id }}
                </h3>
                <button type="button" onclick="closeModal('modal-envio-{{ $pedido->pedido_id }}')" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-4 text-sm divide-y divide-gray-800/60">
                <div class="grid grid-cols-2 gap-4 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">👤 Destinatario</span>
                        <strong class="text-white font-bold block">{{ $nombreClienteModal }}</strong>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">📞 Teléfono</span>
                        <span class="text-emerald-400 font-bold block">{{ $pedido->telefono_final ?? 'No especificado' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">🗺️ Departamento</span>
                        <span class="text-gray-200 font-semibold">{{ $pedido->departamento ?? 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">🏙️ Ciudad</span>
                        <span class="text-gray-200 font-semibold">{{ $pedido->ciudad ?? 'No registrado' }}</span>
                    </div>
                </div>

                <div class="pt-3 pb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block mb-1">🏘️ Barrio</span>
                    <span class="text-indigo-200 font-bold bg-indigo-500/10 border border-indigo-500/20 px-3 py-1 rounded-lg inline-block">
                        {{ $pedido->barrio ?? 'No especificado' }}
                    </span>
                </div>

                <div class="pt-3 pb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">🏠 Dirección Exacta</span>
                    <span class="text-white font-bold block">{{ $pedido->direccion ?? 'No especificada' }}</span>
                </div>
            </div>

            <div class="p-4 bg-gray-950 border-t border-gray-800 flex justify-end">
                <button type="button" onclick="closeModal('modal-envio-{{ $pedido->pedido_id }}')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs rounded-xl cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: PRODUCTOS DEL PEDIDO -->
    <div id="modal-productos-conf-{{ $pedido->pedido_id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full m-4 overflow-hidden shadow-2xl">
            <div class="p-5 bg-gray-950 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                    📦 Productos de la Venta #CONF-{{ str_pad($numeroConsecutivoModal, 4, '0', STR_PAD_LEFT) }}
                </h3>
                <button type="button" onclick="closeModal('modal-productos-conf-{{ $pedido->pedido_id }}')" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-3 max-h-80 overflow-y-auto">
                @forelse($pedido->detalles ?? [] as $item)
                    @php
                        $itemObj  = (object)$item;
                        $nombre   = $itemObj->producto_nombre ?? $itemObj->nombre ?? 'Producto #' . ($itemObj->producto_id ?? '');
                        $talla    = $itemObj->talla ?? $itemObj->numero ?? 'N/A';
                        $cantidad = $itemObj->cantidad ?? $itemObj->cant ?? 1;
                        $precio   = $itemObj->precio_unitario ?? $itemObj->precio ?? 0;
                        $imagen   = $itemObj->producto_imagen ?? $itemObj->imagen ?? null;
                    @endphp

                    <div class="flex justify-between items-center bg-gray-950 p-3.5 rounded-xl border border-gray-800">
                        <div class="flex items-center gap-3">
                            @if($imagen)
                                <img src="{{ asset($imagen) }}" alt="{{ $nombre }}" class="w-12 h-12 object-cover rounded-lg border border-gray-800" onerror="this.style.display='none'">
                            @endif
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $nombre }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Talla: <span class="text-indigo-400 font-bold">{{ $talla }}</span> | 
                                    Cantidad: <span class="text-indigo-400 font-bold">{{ $cantidad }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[11px] text-gray-400">Unitario: ${{ number_format($precio, 0, ',', '.') }}</span>
                            <span class="text-sm font-black text-emerald-400">${{ number_format($precio * $cantidad, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm italic">Sin desglose detallado registrado para esta venta.</p>
                        <p class="text-xs text-gray-600 mt-1">(Esta venta fue registrada antes de activar el historial de detalles)</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-gray-950 border-t border-gray-800 flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">Monto total pagado:</span>
                <span class="text-base font-black text-emerald-400">${{ number_format($pedido->monto_total, 0, ',', '.') }} COP</span>
            </div>
        </div>
    </div>
@endforeach

<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}
</script>
@endsection