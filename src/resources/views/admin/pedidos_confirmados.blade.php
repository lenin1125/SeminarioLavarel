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
                        <th class="p-4">Dirección de Envío</th>
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

                            <!-- Dirección de Envío -->
                            <td class="p-4 text-xs text-gray-300">
                                @if(!empty($pedido->direccion))
                                    <div class="font-bold text-white">{{ $pedido->direccion }}</div>
                                    <div class="text-gray-400 text-[11px]">
                                        {{ $pedido->ciudad ?? '' }}{{ (!empty($pedido->ciudad) && !empty($pedido->departamento)) ? ', ' : '' }}{{ $pedido->departamento ?? '' }}
                                    </div>
                                @else
                                    <span class="text-gray-500 italic">No especificada</span>
                                @endif
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
@endsection