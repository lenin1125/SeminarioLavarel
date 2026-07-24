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

    <!-- ========================================== -->
    <!--     BARRA SUPERIOR ORIGINAL REPLICADA      -->
    <!-- ========================================== -->
    <nav class="bg-[#0b0f19] border-b border-[#111827] px-8 py-4 flex items-center justify-between shadow-sm">
        <!-- Logo Izquierdo Oficial (¡Más grande!) -->
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-[#111827] border border-[#1f2937] flex items-center justify-center overflow-hidden shadow-md">
                <img src="{{ asset('logo.jpg') }}" alt="Logo Zapatillas LH" class="w-full h-full object-cover">
            </div>
            <div>
                <span class="text-white font-black text-lg tracking-wider block">SNEAKERS LH</span>
                <span class="text-gray-400 font-bold text-[11px] tracking-widest block uppercase">TU ESTILO, A CADA PASO</span>
            </div>
        </div>

        <!-- Menú Derecho Original con Contador -->
        <div class="flex items-center gap-4 text-sm font-semibold">
            <!-- Botón Mi Carrito con Burbuja Morada -->
            <a href="{{ route('carrito.index') }}" class="bg-[#111a2e] border border-[#1f2937] hover:bg-[#1a2642] text-gray-200 px-4 py-2.5 rounded-xl flex items-center gap-2 transition-all relative text-xs font-bold">
                🛒 Mi Carrito
                @php
                    $cartCount = session()->has('carrito') ? count(session('carrito')) : 0;
                @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-1.5 bg-[#4f46e5] text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-lg">
                        {{ $cartCount }}
                    </span>
                @else
                    <span class="absolute -top-2 -right-1.5 bg-[#4f46e5] text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-lg">
                        1
                    </span>
                @endif
            </a>
            
            @auth
                @if(Auth::user()->email === 'admin@sneakerslh.com')
                    <a href="{{ route('admin.zapatos.index') }}" class="bg-[#111a2e]/40 hover:bg-[#111a2e] border border-[#1f2937] text-gray-400 hover:text-white px-4 py-2.5 rounded-xl text-xs font-medium transition-all">
                        Panel de administración de ir al →
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-rose-400 text-xs font-bold ml-2 transition-colors cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            @else
                <!-- Botón de Registro -->
                <a href="/registro" class="bg-[#111a2e] border border-[#1f2937] hover:bg-[#1a2642] text-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                    Registrarse
                </a>
                <!-- Botón de Iniciar Sesión -->
                <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                    Iniciar Sesión
                </a>
            @endauth
        </div>
    </nav>

    <!-- ========================================== -->
    <!--  ALERTA DE ÉXITO (COMPROBANTE ENVIADO)    -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="max-w-7xl w-full mx-auto px-8 mt-5">
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-3.5 rounded-xl flex items-center gap-2 shadow-lg animate-fade-in">
                <span>✨</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!--      SECCIÓN HERO CENTRAL (BIENVENIDA)     -->
    <!-- ========================================== -->
    <section class="w-full text-center py-16 bg-gradient-to-b from-[#0b0f19] to-[#060913] border-b border-[#111827]/30">
        <div class="max-w-4xl mx-auto px-6 flex flex-col items-center">
            <!-- Logo Grande Centrado (¡Aumentado a w-40 h-40!) -->
            <div class="w-40 h-40 rounded-3xl bg-[#111827] border border-[#1f2937] flex items-center justify-center shadow-2xl overflow-hidden mb-6">
                <img src="{{ asset('logo.jpg') }}" alt="Logo Grande Zapatillas LH" class="w-full h-full object-cover">
            </div>
            
            <!-- Título e Identidad "SNEAKERS LH" -->
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

    <!-- ========================================== -->
    <!--            FILTROS DE BÚSQUEDA            -->
    <!-- ========================================== -->
    <form method="GET" action="{{ url('/') }}" id="formFiltro" class="bg-gray-950/60 p-6 rounded-2xl border border-gray-800">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
        
        <!-- 1. GÉNERO / ESTILO -->
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                GÉNERO / ESTILO
            </label>
            <select name="estilo" onchange="this.form.submit()" class="w-full bg-gray-900 border border-gray-800 text-white rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none">
                <option value="">Todos los estilos</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('estilo') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 2. PRECIO MÁXIMO (SLIDER DINÁMICO EN TIEMPO REAL) -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                    PRECIO MÁXIMO
                </label>
                <!-- Muestra el precio formateado en tiempo real mientras mueven la barra -->
                <span id="precioFormateado" class="text-sm font-extrabold text-emerald-400">
                    $ {{ number_format(request('precio_max', $precioMaximoCatalogo), 0, ',', '.') }}
                </span>
            </div>

            <!-- Línea Deslizante (Range Input) -->
            <input type="range" 
                   name="precio_max" 
                   id="sliderPrecio"
                   min="0" 
                   max="{{ $precioMaximoCatalogo }}" 
                   step="10000"
                   value="{{ request('precio_max', $precioMaximoCatalogo) }}"
                   oninput="actualizarTextoPrecio(this.value)"
                   onchange="this.form.submit()"
                   class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-emerald-500">

            <div class="flex justify-between text-[10px] text-gray-500 font-bold mt-1">
                <span>$ 0</span>
                <span>$ {{ number_format($precioMaximoCatalogo, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>
</form>

<!-- JS liviano para actualizar el número en pantalla mientras deslizan la barra -->
<script>
function actualizarTextoPrecio(valor) {
    const formateado = new Intl.NumberFormat('es-CO').format(valor);
    document.getElementById('precioFormateado').innerText = '$ ' + formateado;
}
</script>

    <!-- ========================================== -->
    <!--         GRID DE TARJETAS DE TENIS          -->
    <!-- ========================================== -->
    <main class="max-w-7xl w-full mx-auto px-8 my-10 flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
           @forelse($productos as $producto)
    <div class="bg-[#0b0f19] border border-[#1f2937] rounded-2xl overflow-hidden shadow-xl flex flex-col justify-between group hover:border-[#374151] transition-all relative">
        <!-- Contenedor Imagen -->
        <div class="w-full h-64 bg-[#111827] relative overflow-hidden flex items-center justify-center border-b border-[#1f2937]/50">
            @if($producto->imagen_url)
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <span class="text-5xl opacity-40">👟</span>
            @endif

            <!-- Etiqueta de Género flotante (Corregido a $producto->genero) -->
            <span class="absolute top-3 right-3 bg-[#5c2163] text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-md tracking-wider">
                {{ $producto->genero ?? 'UNISEX' }}
            </span>
        </div>

        <!-- Datos del Calzado -->
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black tracking-widest text-indigo-400 uppercase">
                    {{ $producto->categoria->nombre ?? 'URBANO' }}
                </span>
                <h3 class="text-white font-bold text-base mt-1 line-clamp-1 group-hover:text-indigo-400 transition-colors">
                    {{ $producto->nombre }}
                </h3>
            </div>

            <!-- Precio y Botón -->
            <div class="flex items-center justify-between mt-6 pt-4 border-t border-[#1f2937]/50">
                <span class="text-[#10b981] font-black text-lg tracking-wide">
                    ${{ number_format($producto->precio, 0, ',', '.') }}
                </span>
                <a href="{{ route('tienda.show', $producto->id) }}" class="bg-[#4f46e5] hover:bg-[#4338ca] text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all">
                    Ver Detalles
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

    <!-- ========================================== -->
    <!--     PIE DE PÁGINA REPLICADO AL 100%        -->
    <!-- ========================================== -->
    <footer class="bg-[#0b0f19] border-t border-[#111827] py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-12">
            
            <!-- Columna 1: SNEAKERSLH -->
            <div>
                <h4 class="text-white font-black text-sm tracking-wider uppercase mb-5">SNEAKERSLH</h4>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                    Tu destino definitivo para encontrar calzado urbano, deportivo y de edición especial con los mejores estándares del mercado.
                </p>
            </div>
            
            <!-- Columna 2: CONTACTO -->
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

            <!-- Columna 3: REDES SOCIALES -->
            <div>
                <h4 class="text-white font-black text-sm tracking-wider uppercase mb-5">REDES SOCIALES</h4>
                <p class="text-gray-400 text-sm mb-5">
                    Síguenos para enterarte de los lanzamientos exclusivos antes que nadie:
                </p>
                <!-- Círculos de redes sociales con links reales -->
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

        <!-- Línea divisoria y Copyright inferior -->
        <div class="max-w-7xl mx-auto px-8 mt-10 pt-6 border-t border-[#1f2937]/30 text-center">
            <p class="text-xs text-gray-500 font-medium">
                © 2026 SneakersLH. Todos los derechos reservados. | Simulación de Comercio Electrónico Protegido.
            </p>
        </div>
    </footer>

</body>
</html>