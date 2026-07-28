@extends('layouts.admin')

@section('content')
<div class="p-8">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-gray-800 pb-6">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white uppercase">Inventario de Tenis</h1>
            <p class="text-gray-400 text-sm mt-1">Gestión del catálogo principal de Sneakers LH.</p>
        </div>
        <div>
            <a href="{{ route('admin.zapatos.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-lg flex items-center gap-2 uppercase tracking-wider">
                <span>➕</span> Agregar Nuevo Tenis
            </a>
        </div>
    </div>

    <!-- Mensajes de Alerta -->
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-3.5 rounded-xl mb-6 flex items-center gap-2">
            <span>✨</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Barra de Filtros -->
    <div class="bg-[#111827] p-4 rounded-xl mb-6 border border-gray-800">
        <form action="{{ url('/admin/zapatos') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            
            <div class="flex flex-wrap items-center gap-6">
                <!-- Filtro de Categoría -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-300">🔍 CATEGORÍA:</label>
                    <select name="categoria" onchange="this.form.submit()" class="bg-[#1f2937] text-white border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">-- Todas las Categorías --</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Estado Simplificado -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-300">📌 ESTADO:</label>
                    <select name="estado" onchange="this.form.submit()" class="bg-[#1f2937] text-white border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">-- Todos los Estados --</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="deshabilitado" {{ request('estado') == 'deshabilitado' ? 'selected' : '' }}> Agotado</option>
                    </select>
                </div>
            </div>

            <!-- Botón para Limpiar Filtros -->
            @if(request('categoria') || request('estado'))
                <a href="{{ url('/admin/zapatos') }}" class="bg-red-900/30 text-red-400 hover:bg-red-900/50 border border-red-800/50 rounded-lg px-4 py-2 text-sm font-semibold transition">
                    🧹 Limpiar Filtros
                </a>
            @endif

        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950 border-b border-gray-800 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="p-4">Imagen</th>
                        <th class="p-4">Producto</th>
                        <th class="p-4">Categoría</th>
                        <th class="p-4">Precio</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($zapatos as $zapato)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="p-4">
                                <div class="w-14 h-14 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex items-center justify-center">
                                    @if($zapato->imagen_url)
                                        <img src="{{ $zapato->imagen_url }}" alt="{{ $zapato->nombre }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-2xl">👟</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-white block">{{ $zapato->nombre }}</span>
                                <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">{{ $zapato->genero ?? 'UNISEX' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-800 text-gray-300 text-xs font-semibold px-3 py-1 rounded-lg border border-gray-700">
                                    {{ $zapato->categoria->nombre ?? 'Sin Categoría' }}
                                </span>
                            </td>
                            <td class="p-4 font-black text-emerald-400">
                                ${{ number_format($zapato->precio, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                               <td class="p-4 text-center">
    @if($zapato->activo)
        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
            Disponible
        </span>
    @else
        <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
            Agotado
        </span>
    @endif
</td>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.zapatos.edit', $zapato->id) }}" class="bg-gray-800 hover:bg-gray-700 text-gray-200 p-2 rounded-lg text-xs font-bold transition-all border border-gray-700">
                                        ✏️ Editar
                                    </a>

                                    @if($zapato->activo)
                                        <form action="{{ route('admin.zapatos.toggle', $zapato->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de deshabilitar este tenis?')" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 p-2 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                                🚫 Deshabilitar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.zapatos.toggle', $zapato->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de activar este tenis nuevamente?')" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 p-2 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                                ✅ Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">📦</span>
                                <p class="font-semibold text-sm">No se encontraron productos en el inventario.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection