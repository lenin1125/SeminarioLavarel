<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - SneakersLH</title>
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
            <p class="text-xs text-gray-400">Crea tu cuenta para comprar tus tenis favoritos</p>
        </div>

        <!-- ========================================== -->
        <!--           BLOQUE DE ALERTAS              -->
        <!-- ========================================== -->

        <!-- 1. Estado de Sesión -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs font-semibold flex items-center gap-2 shadow-lg">
                <span>✨</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 2. Alerta de error en sesión -->
        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-bold flex items-center gap-3 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 3. Alerta general de errores ($errors) -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-medium flex items-start gap-3 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-rose-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="space-y-1 w-full">
                    <p class="font-bold text-rose-300">No se pudo registrar la cuenta</p>
                    <ul class="list-disc pl-4 space-y-0.5 text-[11px] text-rose-300/90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario de Registro -->
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-400 uppercase mb-1">Nombre Completo</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full bg-[#0b0f17] border @error('name') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition-colors"
                    placeholder="Tu nombre y apellido">
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-1">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-[#0b0f17] border @error('email') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition-colors"
                    placeholder="nombre@correo.com">
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono / WhatsApp -->
            <div>
                <label for="telefono" class="block text-xs font-bold text-gray-400 uppercase mb-1">Teléfono</label>
                <input id="telefono" type="text" name="telefono" value="{{ old('telefono') }}" required
                    class="w-full bg-[#0b0f17] border @error('telefono') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition-colors"
                    placeholder="Ej: 3001234567">
                @error('telefono')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-400 uppercase mb-1">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-[#0b0f17] border @error('password') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition-colors"
                    placeholder="Mínimo 8 caracteres">
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase mb-1">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full bg-[#0b0f17] border @error('password_confirmation') border-rose-500 focus:border-rose-500 @else border-[#374151] focus:border-indigo-500 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition-colors"
                    placeholder="Repite tu contraseña">
            </div>

            <!-- Botón Registro -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg uppercase tracking-wider mt-2 cursor-pointer">
                Registrarse
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-400">
            ¿Ya tienes una cuenta? 
            <a href="{{ route('login') }}" class="text-indigo-400 font-bold hover:underline">Inicia sesión</a>
        </div>
    </div>

</body>
</html>