<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Zapato - Sneakers LH Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            background-color: #060913;
            color: #fff;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen py-10 px-4 md:px-8 flex items-center justify-center">

    <div class="max-w-4xl w-full bg-[#0b0f19] border border-[#1f2937] rounded-3xl p-6 md:p-10 shadow-2xl relative">
        
        <!-- HEADER DE NAVEGACIÓN -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-[#1f2937]">
            <div>
                <a href="{{ route('admin.zapatos.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-2 transition-colors mb-2">
                    ← Volver al Inventario
                </a>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white uppercase">
                    ✏️ Editar Zapato
                </h1>
                <p class="text-xs text-gray-400 mt-1">
                    Actualiza los precios, categoría, imágenes y existencias por talla en inventario.
                </p>
            </div>

            <div class="flex items-center gap-2">
                @php $isActivo = $producto->activo ?? true; @endphp
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl border {{ $isActivo ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400' }}">
                    {{ $isActivo ? '● Producto Activo' : '🚫 Deshabilitado' }}
                </span>
            </div>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <form action="{{ route('admin.zapatos.update', $producto->id) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- FILA 1: NOMBRE Y CATEGORÍA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                        Nombre del Zapato *
                    </label>
                    <input type="text" 
                           name="nombre" 
                           value="{{ old('nombre', $producto->nombre) }}" 
                           required
                           class="w-full bg-[#111827] border border-[#1f2937] text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                        Categoría *
                    </label>
                    <select name="categoria_id" required class="w-full bg-[#111827] border border-[#1f2937] text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ $producto->categoria_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- FILA 2: PRECIO, GÉNERO Y ESTADO -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                        Precio ($ COP) *
                    </label>
                    <input type="number" 
                           name="precio" 
                           value="{{ old('precio', $producto->precio) }}" 
                           min="0" 
                           step="500" 
                           required
                           class="w-full bg-[#111827] border border-[#1f2937] text-emerald-400 font-bold rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                        Género / Estilo
                    </label>
                    <select name="genero" class="w-full bg-[#111827] border border-[#1f2937] text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <option value="UNISEX" {{ ($producto->genero ?? '') == 'UNISEX' ? 'selected' : '' }}>Unisex</option>
                        <option value="HOMBRE" {{ ($producto->genero ?? '') == 'HOMBRE' ? 'selected' : '' }}>Hombre</option>
                        <option value="MUJER" {{ ($producto->genero ?? '') == 'MUJER' ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                        Visibilidad
                    </label>
                    <select name="activo" class="w-full bg-[#111827] border border-[#1f2937] text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <option value="1" {{ $isActivo ? 'selected' : '' }}>Habilitado (En Venta)</option>
                        <option value="0" {{ !$isActivo ? 'selected' : '' }}>Deshabilitado (Agotado/Oculto)</option>
                    </select>
                </div>
            </div>

            <!-- DESCRIPCIÓN -->
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                    Descripción del Producto
                </label>
                <textarea name="descripcion" 
                          rows="3" 
                          class="w-full bg-[#111827] border border-[#1f2937] text-gray-300 rounded-xl p-4 text-sm focus:border-indigo-500 focus:outline-none transition-all placeholder-gray-600"
                          placeholder="Describe detalles, materiales o características destacadas...">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <!-- CONTROL DE TALLAS Y STOCK -->
            <div class="bg-[#111827]/70 border border-[#1f2937] rounded-2xl p-6 mt-6">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-indigo-400 uppercase tracking-wider flex items-center gap-2">
                        📏 Gestión de Tallas y Stock Disponible
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Ingresa la cantidad exacta de pares disponibles para cada talla. Deja en 0 las que estén agotadas.
                    </p>
                </div>

                @php
                    $columnasPivote = \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing('producto_talla');
                    $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

                    // Mapeo de stock existente por ID de talla
                    $stockPorTallaId = [];
                    foreach ($producto->tallas as $t) {
                        $stockPorTallaId[$t->id] = $t->pivot->{$columnaStock} ?? 0;
                    }
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @forelse($tallas as $talla)
                        @php 
                            $numTalla = $talla->numero ?? $talla->nombre ?? $talla->talla ?? $talla->id;
                            $cantActual = $stockPorTallaId[$talla->id] ?? 0; 
                        @endphp
                        <div class="bg-[#0b0f19] border border-[#1f2937] hover:border-indigo-500/50 rounded-xl p-3 flex flex-col justify-between transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-white">EU {{ $numTalla }}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $cantActual > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-800 text-gray-500' }}">
                                    {{ $cantActual > 0 ? $cantActual.' uds' : 'Agotado' }}
                                </span>
                            </div>
                            <input type="number" 
                                   name="stock_tallas[{{ $talla->id }}]" 
                                   value="{{ old('stock_tallas.'.$talla->id, $cantActual) }}" 
                                   min="0" 
                                   placeholder="0"
                                   class="w-full bg-[#111827] border border-[#1f2937] text-white text-center font-extrabold rounded-lg py-1.5 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    @empty
                        <p class="text-xs text-rose-400 col-span-full">No hay tallas registradas en el sistema.</p>
                    @endforelse
                </div>
            </div>

            <!-- FOTO DEL PRODUCTO -->
            <div class="bg-[#111827]/70 border border-[#1f2937] rounded-2xl p-6">
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-3">
                    🖼️ Imagen del Producto
                </label>
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-28 h-28 bg-[#0b0f19] border border-[#1f2937] rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                        @if($producto->imagen_url)
                            <img src="{{ $producto->imagen_url }}" alt="Vista previa" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl opacity-30">👟</span>
                        @endif
                    </div>

                    <div class="flex-1 w-full">
                        <input type="file" 
                               name="imagen_principal" 
                               accept="image/*"
                               class="w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer bg-[#0b0f19] border border-[#1f2937] rounded-xl p-2">
                        <p class="text-[11px] text-gray-500 mt-2">
                            Deja este campo vacío si deseas conservar la imagen actual.
                        </p>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-[#1f2937]">
                <a href="{{ route('admin.zapatos.index') }}" class="bg-[#111a2e] hover:bg-[#1a2642] text-gray-300 border border-[#1f2937] text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-xl transition-all">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider px-8 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                    💾 Guardar Cambios
                </button>
            </div>

        </form>
    </div>

</body>
</html>