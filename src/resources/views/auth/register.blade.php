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

        <!-- Errores -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs font-bold">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-400 uppercase mb-1">Nombre Completo</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="Tu nombre y apellido">
            </div>

            <!-- Correo -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-1">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="nombre@correo.com">
            </div>

            <!-- Teléfono / WhatsApp -->
            <div class="mt-4">
                <label for="telefono" class="block font-medium text-sm text-gray-300">Teléfono </label>
                <input id="telefono" class="block mt-1 w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                    type="text" name="telefono" :value="old('telefono')" required placeholder="Ej: 3001234567" />
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-400 uppercase mb-1">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="Mínimo 8 caracteres">
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase mb-1">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full bg-[#0b0f17] border border-[#374151] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    placeholder="Repite tu contraseña">
            </div>

            <!-- Botón Registro -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg uppercase tracking-wider mt-2">
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