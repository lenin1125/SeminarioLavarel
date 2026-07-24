@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="mb-8 border-b border-gray-800 pb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white uppercase">Usuarios Registrados</h1>
            <p class="text-gray-400 text-sm mt-1">Listado general de clientes registrados en Sneakers LH.</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/30 text-indigo-400 font-bold px-4 py-2 rounded-xl text-xs">
            Total Clientes: {{ $usuarios->total() }}
        </div>
    </div>

    <!-- Mensaje de éxito / error -->
    @if(session('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-xs font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950 border-b border-gray-800 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Correo Electrónico</th>
                        <th class="p-4">Teléfono / WhatsApp</th>
                        <th class="p-4">Fecha Registro</th>
                        <th class="p-4 text-center">Rol</th>
                        <th class="p-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($usuarios as $user)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <!-- Cliente -->
                            <td class="p-4 font-bold text-white">
                                {{ trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? '')) ?: 'Cliente Registrado' }}
                            </td>
                            
                            <!-- Email -->
                            <td class="p-4 text-gray-300 text-xs">{{ $user->email }}</td>
                            
                            <!-- Teléfono -->
                            <td class="p-4">
                                @if($user->telefono)
                                    <a href="https://wa.me/57{{ preg_replace('/[^0-9]/', '', $user->telefono) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 font-bold text-xs hover:underline">
                                        💬 {{ $user->telefono }}
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500 italic">No registrado</span>
                                @endif
                            </td>

                            <!-- Fecha Registro -->
                            <td class="p-4 text-gray-400 text-xs">
                                {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y - h:i A') : 'N/A' }}
                            </td>

                            <!-- Rol -->
                            <td class="p-4 text-center">
                                @if($user->email === 'admin@sneakerslh.com')
                                    <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/30 text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                                        👑 Administrador
                                    </span>
                                @else
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                                        👤 Cliente
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones (Eliminar) -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($user->email !== 'admin@sneakerslh.com')
                                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-gray-600 uppercase font-bold">Protegido</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">👥</span>
                                <p class="font-semibold text-sm">No hay usuarios registrados aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        @if($usuarios->hasPages())
            <div class="p-4 border-t border-gray-800 bg-gray-950/50">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection