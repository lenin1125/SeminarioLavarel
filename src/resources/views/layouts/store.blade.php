<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersLH - Tienda de Calzado sobre Pedido</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <!-- BARRA DE NAVEGACIÓN PÚBLICA -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-black tracking-tight text-indigo-600">
                        SNEAKERS<span class="text-black font-medium">LH</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="/" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">Catálogo</a>
                    
                    @auth
                        <!-- Si el usuario ya inició sesión, ve el Dashboard -->
                        <a href="/dashboard" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">Mi Cuenta</a>
                    @else
                        <!-- Si es un visitante anónimo, ve los botones de ingreso -->
                        <a href="/login" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">Iniciar Sesión</a>
                        <a href="/register" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="bg-white border-t border-gray-100 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
            <p>&copy; 2026 SneakersLH - Proyecto de Grado. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>