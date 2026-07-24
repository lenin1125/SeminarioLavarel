<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SneakersLH</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b0f17] text-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-[#111827] border border-[#1f2937] p-8 rounded-2xl shadow-2xl">
        <!-- Logo e Identidad -->
        <div class="text-center mb-8">
            <a href="{{ route('tienda.index') }}" class="inline-flex items-center gap-3 mb-2">
                <img src="/logo.jpg" alt="SneakersLH" class="h-12 w-12 rounded-xl object-cover">
                <span class="text-2xl font-black tracking-wider text-white">SneakersLH</span>
            </a>
            <p class="text-xs text-gray-400">Ingresa tus credenciales para acceder a tu cuenta</p>
        </div>

        <!-- Errores de validación -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-bold">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-2">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="usuario@ejemplo.com">
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-400 uppercase mb-2">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="••••••••">
            </div>

            <!-- Recordarme -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-400">
                    <input type="checkbox" name="remember" class="rounded bg-[#0b0f17] border-[#374151] text-indigo-600 focus:ring-0">
                    <span>Recordar sesión</span>
                </label>
            </div>

            <!-- Botón Entrar -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg uppercase tracking-wider">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-400">
            ¿Aún no tienes una cuenta? 
            <a href="{{ route('register') }}" class="text-indigo-400 font-bold hover:underline">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>