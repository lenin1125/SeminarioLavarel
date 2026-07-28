<nav class="bg-[#0b0f19] border-b border-[#111827] px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-50">
    <!-- Logo Izquierdo Oficial -->
    <div class="flex items-center gap-4">
        <a href="{{ route('tienda.index') }}" class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#111827] border border-[#1f2937] flex items-center justify-center overflow-hidden shadow-md">
                <img src="{{ asset('logo.jpg') }}" alt="Logo Sneakers LH" class="w-full h-full object-cover">
            </div>
            <div>
                <span class="text-white font-black text-lg tracking-wider block leading-none">SNEAKERS LH</span>
                <span class="text-gray-400 font-bold text-[10px] tracking-widest block uppercase mt-1">TU ESTILO, A CADA PASO</span>
            </div>
        </a>
    </div>

    <!-- Menú Derecho con Contador de Carrito -->
    <div class="flex items-center gap-4 text-sm font-semibold">
        <!-- Botón Mi Carrito -->
        <a href="{{ route('carrito.index') }}" class="bg-[#111a2e] border border-[#1f2937] hover:bg-[#1a2642] text-gray-200 px-4 py-2.5 rounded-xl flex items-center gap-2 transition-all relative text-xs font-bold">
            🛒 Mi Carrito
            @php
                $cartCount = session()->has('carrito') ? count(session('carrito')) : 0;
            @endphp
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-1.5 bg-[#4f46e5] text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-lg">
                    {{ $cartCount }}
                </span>
            @endif
        </a>
        
        @auth
            <!-- Panel Administrador (Lógica limpia reutilizando el modelo User) -->
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.zapatos.index') }}" class="bg-[#111a2e] border border-indigo-500/20 text-indigo-400 font-bold px-3 py-2 rounded-lg hover:bg-indigo-500/20 transition flex items-center gap-1 text-xs">
                    ⚙️ Panel Administrador &rarr;
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-rose-400 text-xs font-bold ml-2 transition-colors cursor-pointer">
                    Cerrar Sesión
                </button>
            </form>
        @else
            <!-- Enlaces para visitantes no autenticados -->
            <a href="{{ route('register') }}" class="bg-[#111a2e] border border-[#1f2937] hover:bg-[#1a2642] text-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                Registrarse
            </a>
            <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                Iniciar Sesión
            </a>
        @endauth
    </div>
</nav>