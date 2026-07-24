@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="border-b border-gray-800 pb-5">
        <h1 class="text-3xl font-black tracking-tight text-white uppercase">Verificación de Pagos Pendientes</h1>
        <p class="text-gray-400 text-xs mt-1">Lista de transacciones que requieren revisión visual del comprobante.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-3.5 rounded-xl">
            ✨ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-bold px-4 py-3.5 rounded-xl">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950 border-b border-gray-800 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="p-4">Pedidos</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Monto Total</th>
                        <th class="p-4 text-center">Comprobante</th>
                        <th class="p-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($pagosPorVerificar as $pago)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <!-- Muestra PED #1, PED #2, etc. -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="font-black text-indigo-400 bg-indigo-500/10 border border-indigo-500/30 px-3 py-1.5 rounded-lg text-xs">
                                    PED #{{ $loop->iteration }}
                                </span>
                            </td>

                            <td class="p-4 font-bold text-white">{{ $pago->nombre }} {{ $pago->apellido }}</td>
                            <td class="p-4 font-black text-emerald-400">${{ number_format($pago->total, 0, ',', '.') }} COP</td>
                            
                            <td class="p-4 text-center">
                                @if($pago->comprobante)
                                    <a href="{{ $pago->comprobante }}" target="_blank" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow">
                                        👁️ Ver Imagen
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500">Sin recibo</span>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Aprobar -->
                                    <form action="{{ route('admin.pedidos.aprobar', $pago->pedido_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-black text-xs px-3.5 py-2 rounded-lg transition-all shadow">
                                            ✅ Aprobar
                                        </button>
                                    </form>

                                    <!-- Rechazar / Eliminar -->
                                    <form action="{{ route('admin.pedidos.rechazar', $pago->pedido_id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas RECHAZAR y eliminar este pedido?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 font-bold text-xs px-3.5 py-2 rounded-lg transition-all">
                                            ❌ Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">🎉</span>
                                <p class="font-semibold text-sm">No hay pagos pendientes por verificar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection