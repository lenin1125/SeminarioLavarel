<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersLH - Tienda Oficial</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen flex flex-col">

    <!-- NAVIGATION BAR -->
    <nav class="bg-gray-900 border-b border-gray-800 shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo e Imagen -->
                <div class="flex items-center">
                    <a href="{{ route('tienda.index') }}" class="flex items-center gap-2.5">
                        <img src="{{ asset('logo.jpg') }}" 
                             alt="SneakersLH Logo" 
                             class="h-9 w-auto object-contain rounded-md">
                        <span class="text-xl font-black tracking-tight text-white">SneakersLH</span>
                    </a>
                </div>

                <!-- Menú de Navegación -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('tienda.index') }}" class="text-sm font-semibold text-gray-300 hover:text-indigo-400 transition">Tienda</a>
                    
                    @auth
                        <!-- Carrito -->
                        <a href="{{ route('carrito.index') }}" class="text-sm font-semibold text-gray-300 hover:text-indigo-400 transition relative flex items-center gap-1.5">
                            🛒 Mi Carrito
                            @if(session()->has('carrito') && count(session('carrito')) > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                                    {{ count(session('carrito')) }}
                                </span>
                            @endif
                        </a>

                        <!-- PANEL ADMIN -->
                        @if(Auth::user()->email === 'admin@sneakerslh.com')
                            <a href="{{ route('admin.zapatos.index') }}" class="text-sm font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-3 py-1 rounded-lg hover:bg-indigo-500/20 transition">
                                🛠️ Panel Admin
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Control de Sesión (Derecha) -->
                <div class="flex items-center gap-4">
                    @auth
                        {{-- Ocultar el botón si la ruta actual es checkout --}}
                        @unless(request()->routeIs('checkout*'))
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 font-bold text-xs px-4 py-2 rounded-xl transition border border-red-500/20 cursor-pointer">
                                    Cerrar Sesión
                                </button>
                            </form>
                        @endunless
                    @else
                        <div class="flex items-center gap-2">
                            <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-semibold text-xs px-4 py-2 transition">
                                Iniciar Sesión
                            </a>
                            <a href="/registro" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-lg shadow-indigo-600/30">
                                Registrarse
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="py-6 flex-1">
        @yield('content')
    </main>

</body>
</html>