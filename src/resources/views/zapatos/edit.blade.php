@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 my-6">
    
    <div class="mb-6 border-b border-gray-100 pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Editar Zapato</h2>
        <p class="text-sm text-gray-500 mt-1">Modifica los detalles del producto en pesos colombianos (COP). Si no deseas cambiar la foto, deja el campo vacío.</p>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg text-sm font-medium">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.zapatos.update', $producto->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre del Zapato</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 font-medium">
        </div>

        <!-- Campo Actualizado: Selector de Categorías para Edición -->
        <div>
            <label for="categoria_id" class="block text-sm font-semibold text-gray-700 mb-1">Categoría</label>
            <select name="categoria_id" id="categoria_id" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 font-medium bg-white">
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1">Descripción (Opcional)</label>
            <textarea name="descripcion" id="descripcion" rows="3"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div>
            <label for="precio" class="block text-sm font-semibold text-gray-700 mb-1">Precio ($ COP)</label>
            <input type="number" name="precio" id="precio" min="0" value="{{ old('precio', intval($producto->precio)) }}" required
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 font-bold">
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <div class="text-center">
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Imagen Actual</span>
                <div class="w-20 h-20 bg-white rounded-lg flex items-center justify-center overflow-hidden border shadow-sm">
                    @if($producto->imagen_url)
                        <img src="{{ asset('storage/' . $producto->imagen_url) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl">👟</span>
                    @endif
                </div>
            </div>

            <div class="flex-1 w-full">
                <label for="imagen" class="block text-sm font-semibold text-gray-700 mb-1">Subir Nueva Foto (Reemplazar)</label>
                <input type="file" name="imagen" id="imagen" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-xs text-gray-400 mt-1">Formatos admitidos: JPEG, PNG, JPG, WEBP. Máx: 2MB.</p>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.zapatos.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 transition">
                Cancelar
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg transition shadow-sm text-sm">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection