<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNEAKERS LH - TU ESTILO, A CADA PASO</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            background-color: #060913;
            color: #fff;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <!-- BARRA NAVEGACIÓN REUTILIZABLE -->
    @include('layouts.navigation')

    <!-- ALERTA DE ÉXITO, ERRORES Y VALIDACIÓN -->
    @if(session('success'))
        <div class="max-w-7xl w-full mx-auto px-8 mt-5">
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-3.5 rounded-xl flex items-center gap-2 shadow-lg animate-fade-in">
                <span>✨</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl w-full mx-auto px-8 mt-5">
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-bold px-4 py-3.5 rounded-xl flex items-center gap-2 shadow-lg animate-fade-in">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl w-full mx-auto px-8 mt-5">
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-bold px-4 py-3.5 rounded-xl flex flex-col gap-1.5 shadow-lg animate-fade-in">
                <div class="flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Atención:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 font-medium text-rose-300/90 pl-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- SECCIÓN HERO CENTRAL -->
    <section class="w-full text-center py-16 bg-gradient-to-b from-[#0b0f19] to-[#060913] border-b border-[#111827]/30">
        <div class="max-w-4xl mx-auto px-6 flex flex-col items-center">
            <div class="w-40 h-40 rounded-3xl bg-[#111827] border border-[#1f2937] flex items-center justify-center shadow-2xl overflow-hidden mb-6">
                <img src="{{ asset('logo.jpg') }}" alt="Logo Grande Zapatillas LH" class="w-full h-full object-cover">
            </div>
            
            <h2 class="text-white text-5xl md:text-6xl font-black tracking-tighter uppercase mb-3">
                SNEAKERS LH
            </h2>
            <p class="text-indigo-400 font-serif italic text-lg md:text-xl mb-4 tracking-wide">
                "Tu estilo, a cada paso"
            </p>
            <p class="text-gray-500 text-sm max-w-xl leading-relaxed">
                Explora la última colección de tenis urbanos con envíos garantizados a todo el país.
            </p>
        </div>
    </section>

    <!-- FILTROS DE BÚSQUEDA -->
    <div class="max-w-7xl w-full mx-auto px-8 mt-8">
        <form method="GET" action="{{ url('/') }}" id="formFiltro" class="bg-gray-950/60 p-6 rounded-2xl border border-gray-800 flex flex-col md:flex-row items-end gap-6 justify-between">
            
            <div class="w-full md:flex-1">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                    GÉNERO / ESTILO
                </label>
                <select name="estilo" onchange="this.form.submit()" class="w-full bg-gray-900 border border-gray-800 text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none cursor-pointer">
                    <option value="">Todos los estilos</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('estilo') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:flex-1">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                        PRECIO MÁXIMO
                    </label>
                    <span id="precioFormateado" class="text-sm font-extrabold text-emerald-400">
                        $ {{ number_format(request('precio_max', $precioMaximoCatalogo ?? 500000), 0, ',', '.') }}
                    </span>
                </div>

                <input type="range" 
                       name="precio_max" 
                       id="sliderPrecio"
                       min="0" 
                       max="{{ $precioMaximoCatalogo ?? 500000 }}" 
                       step="10000"
                       value="{{ request('precio_max', $precioMaximoCatalogo ?? 500000) }}"
                       oninput="actualizarTextoPrecio(this.value)"
                       onchange="this.form.submit()"
                       class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-emerald-500 my-3">

                <div class="flex justify-between text-[10px] text-gray-500 font-bold">
                    <span>$ 0</span>
                    <span>$ {{ number_format($precioMaximoCatalogo ?? 500000, 0, ',', '.') }}</span>
                </div>
            </div>

            @if(count(request()->except('page')) > 0)
                <div class="w-full md:w-auto flex-shrink-0">
                    <a href="{{ url('/') }}" class="w-full md:w-auto bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold px-5 h-[46px] rounded-xl transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                        <span>🧹</span> Limpiar Filtros
                    </a>
                </div>
            @endif

        </form>
    </div>

    <script>
    function actualizarTextoPrecio(valor) {
        const formateado = new Intl.NumberFormat('es-CO').format(valor);
        document.getElementById('precioFormateado').innerText = '$ ' + formateado;
    }
    </script>

    <!-- GRID DE TARJETAS -->
    <main class="max-w-7xl w-full mx-auto px-8 my-10 flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($productos as $producto)
                @php 
                    $isActivo = $producto->activo ?? true; 
                @endphp
                <div class="bg-[#0b0f19] border border-[#1f2937] rounded-2xl overflow-hidden shadow-xl flex flex-col justify-between group hover:border-[#374151] transition-all relative">
                    
                    <div class="w-full h-64 bg-[#111827] relative overflow-hidden flex items-center justify-center border-b border-[#1f2937]/50">
                        @if($producto->imagen_url)
                            <img src="{{ $producto->imagen_url }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ !$isActivo ? 'grayscale opacity-50' : '' }}">
                        @else
                            <span class="text-5xl opacity-40">👟</span>
                        @endif

                        @if(!$isActivo)
                            <span class="absolute top-3 left-3 bg-rose-600 border border-rose-500 text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-md tracking-wider shadow-md z-10">
                                🚫 Agotado
                            </span>
                        @endif

                        <span class="absolute top-3 right-3 bg-[#5c2163] text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-md tracking-wider z-10">
                            {{ $producto->genero ?? 'UNISEX' }}
                        </span>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-black tracking-widest text-indigo-400 uppercase">
                                {{ $producto->categoria->nombre ?? 'URBANO' }}
                            </span>
                            <h3 class="text-white font-bold text-base mt-1 line-clamp-1 group-hover:text-indigo-400 transition-colors">
                                {{ $producto->nombre }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-[#1f2937]/50">
                            <span class="{{ $isActivo ? 'text-[#10b981]' : 'text-gray-500 line-through' }} font-black text-lg tracking-wide">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </span>

                            <a href="{{ route('tienda.show', $producto->id) }}" class="{{ $isActivo ? 'bg-[#4f46e5] hover:bg-[#4338ca] text-white' : 'bg-gray-800 hover:bg-gray-700 text-gray-400 border border-gray-700' }} font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all">
                                {{ $isActivo ? 'Ver Detalles' : 'Ver Producto' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <span class="text-4xl">🔍</span>
                    <p class="text-gray-500 font-semibold text-sm mt-3">No encontramos tenis que coincidan con tus filtros.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="bg-[#0b0f19] border-t border-[#111827] py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-12">
            <div>
                <h4 class="text-white font-black text-sm tracking-wider uppercase mb-5">SNEAKERSLH</h4>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                    Tu destino definitivo para encontrar calzado urbano, deportivo y de edición especial con los mejores estándares del mercado.
                </p>
            </div>
            
            <div>
                <h4 class="text-white font-black text-sm tracking-wider uppercase mb-5">CONTACTO</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li class="flex items-center gap-3">
                        <span class="text-[#e056fd]">📞</span> 
                        <span>Teléfono: +57 318 525 2717</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-white">✉️</span> 
                        <span>Correo: sneakerslh@gmail.com</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-black text-sm tracking-wider uppercase mb-5">REDES SOCIALES</h4>
                <p class="text-gray-400 text-sm mb-5">
                    Síguenos para enterarte de los lanzamientos exclusivos antes que nadie:
                </p>
                <div class="flex gap-3">
                    <a href="https://www.facebook.com/share/1HU2q3uTW6/?mibextid=wwXIfr" target="_blank" class="w-10 h-10 rounded-full bg-[#111827] hover:bg-[#1f2937] border border-[#1f2937] flex items-center justify-center text-xs font-bold text-gray-300 hover:text-white transition-all shadow-md">
                        FB
                    </a>
                    <a href="https://www.instagram.com/sneaker_lh?igsh=MWZuMGZtMTNrb20wZg==" target="_blank" class="w-10 h-10 rounded-full bg-[#111827] hover:bg-[#1f2937] border border-[#1f2937] flex items-center justify-center text-xs font-bold text-gray-300 hover:text-white transition-all shadow-md">
                        IG
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-8 mt-10 pt-6 border-t border-[#1f2937]/30 text-center">
            <p class="text-xs text-gray-500 font-medium">
                © 2026 SneakersLH. Todos los derechos reservados. | Simulación de Comercio Electrónico Protegido.
            </p>
        </div>
    </footer>

</body>
</html>