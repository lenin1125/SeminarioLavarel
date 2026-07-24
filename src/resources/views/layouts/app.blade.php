<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersLH - Tienda Oficial</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- NAVIGATION BAR -->
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('tienda.index') }}" class="text-2xl font-black tracking-tight text-indigo-600 flex items-center gap-1">
                        <span>👟</span> SneakersLH
                    </a>
                </div>

                <!-- Menú de Navegación -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('tienda.index') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition">Tienda</a>
                    
                    @auth
                        <!-- El Carrito solo se muestra y funciona si está logueado -->
                        <a href="{{ route('carrito.index') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition relative flex items-center gap-1">
                            🛒 Mi Carrito
                            @if(session()->has('carrito') && count(session('carrito')) > 0)
                                <span class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                                    {{ count(session('carrito')) }}
                                </span>
                            @endif
                        </a>

                        <!-- PANEL ADMIN: SOLO visible si el correo pertenece al administrador -->
                        @if(Auth::user()->email === 'admin@sneakerslh.com')
                            <a href="{{ route('admin.zapatos.index') }}" class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg hover:bg-indigo-100 transition">
                                🛠️ Panel Admin
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Control de Sesión Dinámico (Derecha) -->
                <div class="flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-600 hidden sm:inline">
                                👋 Hola, <strong class="text-gray-900 font-bold">{{ Auth::user()->name }}</strong>
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs px-4 py-2 rounded-full transition shadow-sm border border-red-100">
                                    Cerrar Sesión 🚪
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Si es visitante, muestra botones limpios de ingreso -->
                        <div class="flex items-center gap-2">
                            <a href="/login" class="text-indigo-600 hover:text-indigo-700 font-semibold text-xs px-4 py-2">
                                Iniciar Sesión
                            </a>
                            <a href="/registro" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-full transition shadow-sm">
                                Registrarse
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="py-6">
        @yield('content')
    </main>

</body>
</html>