@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Encabezado de Acción -->
    <div class="mb-8">
        <h2 class="text-3xl font-black text-white tracking-tight uppercase">Añadir Nuevo Zapato</h2>
        <p class="text-sm text-gray-400 mt-1">Ingresa el modelo especificando el público objetivo (Hombre, Mujer o Unisex) y sus respectivas tallas.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-950/40 border border-red-800 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm">
            <span class="font-bold">Por favor corrige los siguientes errores:</span>
            <ul class="list-disc list-inside mt-1 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario Premium -->
    <form action="{{ route('admin.zapatos.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-900 border border-gray-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Nombre del Producto -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Nombre del Zapato</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Nike Air Force 1 '07" required
                       class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-medium transition">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Categoría</label>
                <select name="categoria_id" required class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-medium transition">
                    <option value="" disabled selected>Selecciona una opción</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Precio -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Precio de Venta ($ COP)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-500 font-bold text-sm">$</span>
                    <input type="number" name="precio" value="{{ old('precio') }}" placeholder="450000" min="0" required
                           class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-8 pr-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-medium transition">
                </div>
            </div>

            <!-- Clasificación / Género -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Clasificación / Género</label>
                <select name="genero" required class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-medium transition">
                    <option value="Unisex" {{ old('genero') == 'Unisex' ? 'selected' : '' }}>Unisex (Para todos)</option>
                    <option value="Hombre" {{ old('genero') == 'Hombre' ? 'selected' : '' }}>Caballeros / Hombre</option>
                    <option value="Mujer" {{ old('genero') == 'Mujer' ? 'selected' : '' }}>Damas / Mujer</option>
                </select>
            </div>
        </div>

        <!-- Descripción -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Descripción o Detalles del Calzado</label>
            <textarea name="descripcion" rows="3" placeholder="Describe los materiales, colores principales o cualquier característica exclusiva..."
                      class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-medium transition">{{ old('descripcion') }}</textarea>
        </div>

        <!-- SECCIÓN DE TALLAS CORREGIDA Y EXTRA FLEXIBLE -->
        <div class="border-t border-gray-800 pt-5">
            <label class="block text-xs font-black uppercase tracking-widest text-indigo-400 mb-4">Disponibilidad de Tallas (EURO)</label>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bloque de Tallas para Mujer (36 a 39) -->
                <div class="bg-gray-950/50 p-4 rounded-xl border border-gray-800/60">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-sm">👩</span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-pink-400">Tallas para Damas (36 - 39)</h4>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($tallas as $talla)
                            @php
                                // Filtra solo los números extrayendo cualquier carácter extraño
                                $numeroLimpio = intval(preg_replace('/[^0-9]/', '', $talla->numero));
                            @endphp
                            @if($numeroLimpio >= 36 && $numeroLimpio <= 39)
                                <label class="flex items-center justify-between p-2.5 bg-gray-900 border border-gray-800 rounded-lg cursor-pointer hover:border-gray-700 select-none transition">
                                    <span class="text-xs font-bold text-gray-300">EU {{ $talla->numero }}</span>
                                    <input type="checkbox" name="tallas[]" value="{{ $talla->id }}" class="w-4 h-4 rounded border-gray-700 bg-gray-950 text-indigo-600 focus:ring-0">
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Bloque de Tallas para Hombre (40 a 44) -->
                <div class="bg-gray-950/50 p-4 rounded-xl border border-gray-800/60">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-sm">👨</span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-blue-400">Tallas para Caballeros (40 - 44)</h4>
                    </div>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($tallas as $talla)
                            @php
                                // Filtra solo los números extrayendo cualquier carácter extraño
                                $numeroLimpio = intval(preg_replace('/[^0-9]/', '', $talla->numero));
                            @endphp
                            @if($numeroLimpio >= 40 && $numeroLimpio <= 44)
                                <label class="flex items-center justify-between p-2.5 bg-gray-900 border border-gray-800 rounded-lg cursor-pointer hover:border-gray-700 select-none transition">
                                    <span class="text-xs font-bold text-gray-300">EU {{ $talla->numero }}</span>
                                    <input type="checkbox" name="tallas[]" value="{{ $talla->id }}" class="w-4 h-4 rounded border-gray-700 bg-gray-950 text-indigo-600 focus:ring-0">
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

                    <!-- Sección de Stock por Tallas -->
            <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800 space-y-4">
                <h3 class="text-white font-bold text-sm uppercase tracking-wider">Stock y Unidades por Talla</h3>
                <p class="text-xs text-gray-400">Ingresa la cantidad disponible para cada talla. Si pones 0 o lo dejas vacío, no habrá stock.</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    @foreach($tallas as $talla)
                        <div class="bg-gray-950 p-3 rounded-xl border border-gray-800 text-center">
                            <label class="block text-xs font-bold text-indigo-400 mb-2 uppercase">
                                Talla {{ $talla->numero ?? $talla->nombre ?? $talla->talla }}
                            </label>
                            <!-- Usamos el ID de la talla como clave ($talla->id) -->
                            <input type="number" 
                                name="stock_tallas[{{ $talla->id }}]" 
                                value="0" 
                                min="0" 
                                placeholder="0" 
                                class="w-full bg-gray-900 border border-gray-800 text-white text-center font-bold rounded-lg py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- Imágenes de Portada y Galería -->
        <div class="border-t border-gray-800 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Foto Principal (Portada)</label>
                <input type="file" name="imagen_principal" accept="image/*" required
                       class="w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white cursor-pointer bg-gray-950 border border-gray-800 p-2 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Fotos Adicionales (Ángulos Opcionales)</label>
                <input type="file" name="imagenes_galeria[]" accept="image/*" multiple
                       class="w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-800 file:text-white cursor-pointer bg-gray-950 border border-gray-800 p-2 rounded-xl">
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="border-t border-gray-800 pt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.zapatos.index') }}" class="px-5 py-3 rounded-xl text-xs font-bold bg-gray-800 hover:bg-gray-750 text-gray-300 hover:text-white border border-gray-700 transition">Cancelar</a>
            <button type="submit" class="px-6 py-3 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/10 transition">Guardar Producto</button>
        </div>
    </form>
</div>
@endsection