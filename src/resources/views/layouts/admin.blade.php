<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - SneakersLH</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Barra lateral de navegación) -->
        <div class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col justify-between">
            <div>
                <!-- Identidad de Marca -->
                <div class="p-6 border-b border-gray-800 flex items-center space-x-3">
                    <img src="{{ asset('logo.jpg') }}" alt="SneakersLH Logo" class="w-10 h-10 rounded-lg object-cover border border-gray-700 shadow-md">
                    <div class="flex flex-col">
                        <span class="text-md font-black tracking-wider text-white uppercase leading-none">SneakersLH</span>
                        <span class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest mt-1">Control Panel</span>
                    </div>
                </div>

                <!-- Menú de Enlaces de Navegación -->
                <nav class="p-4 space-y-1.5">
                    <span class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Módulos</span>
                    
                    <!-- 1. Inventario de Tenis -->
                    <a href="{{ route('admin.zapatos.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ Request::is('admin/zapatos*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span>👟</span>
                        <span>Inventario Tenis</span>
                    </a>

                    <!-- 2. Validar Comprobantes (Verificación de Pagos) -->
                    <a href="{{ route('admin.pagos.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ Request::is('admin/pagos*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span>💳</span>
                        <span>Validar Comprobantes</span>
                    </a>

                    <!-- 3. Pedidos (Módulo Unificado) -->
                    <a href="{{ route('admin.pedidos.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ Request::is('admin/pedidos*') || Request::is('admin/pedidos-confirmados*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span>📦</span>
                        <span>Pedidos</span>
                    </a>

                    <!-- 4. Reportes y Ventas -->
                    <a href="{{ route('admin.reportes.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ Request::is('admin/reportes*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span>📊</span>
                        <span>Reportes y Ventas</span>
                    </a>

                    <!-- 5. Gestión de Usuarios -->
                    <a href="{{ route('admin.usuarios.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ Request::is('admin/usuarios*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span>👥</span>
                        <span>Gestión de Usuarios</span>
                    </a>
                </nav>
            </div>

            <!-- Botón inferior para salir al catálogo público -->
            <div class="p-4 border-t border-gray-800">
                <a href="{{ route('tienda.index') }}" class="w-full bg-gray-800 hover:bg-gray-750 text-gray-300 hover:text-white font-bold py-2 px-4 rounded-xl text-xs text-center block transition border border-gray-700/50 shadow-inner">
                    🌐 Ver Tienda Pública
                </a>
            </div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="flex-1 flex flex-col overflow-y-auto bg-gray-950">
            
            <!-- Barra Superior (Únicamente Botón de Cerrar Sesión arriba a la derecha) -->
            <header class="h-16 bg-gray-900/50 backdrop-blur-md border-b border-gray-800 px-8 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center space-x-2 text-xs font-semibold text-gray-400">
                    <span>Admin</span>
                    <span>/</span>
                    <span class="text-indigo-400 font-bold">Panel de Control</span>
                </div>

                <!-- Botón de Cerrar Sesión -->
                <div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-bold text-red-400 hover:text-white bg-red-950/40 hover:bg-red-900 border border-red-800/60 transition cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Inyección de Contenido -->
            <div class="p-8 max-w-6xl w-full mx-auto">
                @yield('content')
            </div>

        </div>
    </div>

</body>
</html>