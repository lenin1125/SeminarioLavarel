<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersLH - Tienda Oficial</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#060913] text-gray-100 font-sans antialiased min-h-screen flex flex-col">

    <!-- BARRA NAVEGACIÓN REUTILIZABLE -->
    @include('layouts.navigation')

    <!-- Contenido Principal -->
    <main class="py-6 flex-1">
        @yield('content')
    </main>

</body>
</html>