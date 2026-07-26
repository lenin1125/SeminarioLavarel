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

        <!-- ========================================== -->
        <!--           BLOQUE DE ALERTAS              -->
        <!-- ========================================== -->

        <!-- 1. Mensajes de estado (ej: Sesión cerrada o reseteo de clave) -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs font-semibold flex items-center gap-2 shadow-lg">
                <span>✨</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 2. Alerta cuando el controlador manda session('error') -->
        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-bold flex items-center gap-3 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 3. Alerta de errores de validación ($errors) -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-medium flex items-start gap-3 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-rose-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="space-y-1 w-full">
                    <p class="font-bold text-rose-300">No se pudo iniciar sesión</p>
                    <ul class="list-disc pl-4 space-y-0.5 text-[11px] text-rose-300/90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-2">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-[#0b0f17] border @error('email') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-3 text-sm text-white focus:outline-none transition-colors"
                    placeholder="usuario@ejemplo.com">
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-400 uppercase mb-2">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-[#0b0f17] border @error('password') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-3 text-sm text-white focus:outline-none transition-colors"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-400">
                    <input type="checkbox" name="remember" class="rounded bg-[#0b0f17] border-[#374151] text-indigo-600 focus:ring-0">
                    <span>Recordar sesión</span>
                </label>
            </div>

            <!-- Botón Entrar -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg uppercase tracking-wider cursor-pointer">
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