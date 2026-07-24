<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre }} - SneakersLH</title>
    <!-- Cargar Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* BASE OSCURA CORPORATIVA */
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #0b0f17;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* HEADER / NAVBAR PREMIUM */
        header {
            background-color: #111827;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #1f2937;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .logo-img {
            height: 45px;
            width: auto;
            object-fit: contain;
            border-radius: 8px;
        }

        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
        }

        .logo-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .logo-subtitle {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-nav {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            color: #ffffff;
            background: #1f2937;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-nav:hover {
            background: #374151;
        }

        /* CONTENEDOR PRINCIPAL DEL DETALLE */
        main {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: #111827;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 40px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
        }

        /* GALERÍA / IMAGEN */
        .image-viewer {
            width: 100%;
            height: 450px;
            background: #0b0f17;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #1f2937;
        }

        .image-viewer img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* BLOQUE DE INFORMACIÓN */
        .info-panel {
            display: flex;
            flex-direction: column;
        }

        .gender-badge {
            align-self: flex-start;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            background: #4c1d95;
            padding: 6px 14px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .product-title {
            font-size: 36px;
            font-weight: 900;
            margin: 0 0 5px 0;
            color: #ffffff;
        }

        .product-category {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 25px;
        }

        .product-category strong {
            color: #ffffff;
        }

        .price-display {
            font-size: 32px;
            font-weight: 800;
            color: #6366f1;
            margin-bottom: 30px;
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .description-text {
            font-size: 15px;
            color: #9ca3af;
            line-height: 1.6;
            margin: 0 0 35px 0;
        }

        /* CAJA DE COMPRA INTERACTIVA */
        .purchase-box {
            background: #0b0f17;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 25px;
        }

        .btn-add-cart {
            width: 100%;
            padding: 15px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 15px;
        }

        .btn-add-cart:hover {
            background: #4f46e5;
        }

        .back-action-link {
            display: inline-block;
            margin-top: 25px;
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .back-action-link:hover {
            color: white;
        }
    </style>
</head>
<body>

    <!-- MENÚ SUPERIOR -->
    <header>
        <a href="{{ route('tienda.index') }}" class="logo-box">
            <img src="/logo.jpg" alt="Logo" class="logo-img">
            <div class="brand-text-wrapper">
                <span class="logo-title">SneakersLH</span>
                <span class="logo-subtitle">Tu estilo, a cada paso</span>
            </div>
        </a>
        
        <div class="nav-actions">
            @auth
                <a href="{{ route('carrito.index') }}" class="btn-nav">🛒 Mi Carrito</a>
            @else
                <a href="/login" class="btn-nav" style="background:#6366f1;">Ingresar</a>
            @endauth
        </div>
    </header>

    <!-- BLOQUE PRINCIPAL -->
    <main>
        <div class="details-grid">
            
            <!-- DETALLE DE FOTO -->
            <div class="image-viewer">
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
            </div>

            <!-- CONTENEDOR DE COMPRA -->
            <div class="info-panel">
                <span class="gender-badge">{{ $producto->genero ?? 'Unisex' }}</span>
                <h1 class="product-title">{{ $producto->nombre }}</h1>
                <div class="product-category">Categoría: <strong>{{ $producto->categoria->nombre ?? 'Urbano' }}</strong></div>
                
                <div class="price-display">${{ number_format($producto->precio, 0, ',', '.') }} COP</div>
                
                <div class="section-label">Descripción:</div>
                <p class="description-text">
                    {{ $producto->descripcion ?? 'Este modelo exclusivo no cuenta con una descripción detallada todavía pero mantiene la calidad prémium de nuestra marca.' }}
                </p>

                <!-- FORMULARIO DE COMPRA CON LÍMITE DINÁMICO DE STOCK -->
                <div class="purchase-box">
                    <form action="{{ route('carrito.agregar', $producto->id) }}" method="POST">
                        @csrf
                        
                        <!-- SELECCIÓN DE TALLA CON ATRIBUTO DATA-STOCK -->
                        <div class="mb-6 space-y-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">
                                Selecciona tu Talla (EU):
                            </label>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @forelse($producto->tallas as $talla)
                                    @php
                                        $numeroTalla = $talla->talla ?? $talla->numero ?? $talla->nombre ?? $talla->id;
                                        // Obtener la cantidad/stock desde la tabla pivote
                                        $stockDisponible = $talla->pivot->stock ?? $talla->pivot->cantidad ?? $talla->stock ?? 10;
                                    @endphp

                                    <label class="cursor-pointer relative block group">
                                        <input type="radio" 
                                               name="talla" 
                                               value="{{ $numeroTalla }}" 
                                               data-stock="{{ $stockDisponible }}"
                                               onchange="actualizarLimiteStock(this)"
                                               class="peer hidden" 
                                               required>
                                        
                                        <div class="p-3 bg-gray-900 border border-gray-800 rounded-xl text-center transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-600/20 group-hover:border-gray-700">
                                            <span class="block text-white font-bold text-sm">EU {{ $numeroTalla }}</span>
                                            <span class="block text-[11px] {{ $stockDisponible > 0 ? 'text-emerald-400' : 'text-red-400' }} font-semibold mt-0.5">
                                                {{ $stockDisponible > 0 ? $stockDisponible . ' disp.' : 'Agotado' }}
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full py-2 text-gray-500 text-xs italic">
                                        No hay tallas asignadas a este zapato.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- SELECCIÓN DE CANTIDAD ADAPTATIVA -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Cantidad:
                                </label>
                                <span id="textoMaxStock" class="text-xs text-indigo-400 font-semibold">
                                    Selecciona una talla
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        onclick="cambiarCantidad(-1)" 
                                        class="w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-xl transition flex items-center justify-center text-lg">
                                    -
                                </button>
                                
                                <input type="number" 
                                       id="cantidadInput" 
                                       name="cantidad" 
                                       value="1" 
                                       min="1" 
                                       max="1" 
                                       readonly 
                                       required 
                                       class="w-20 bg-gray-900 border border-gray-800 text-white text-center font-bold text-base rounded-xl py-2 focus:outline-none">
                                
                                <button type="button" 
                                        onclick="cambiarCantidad(1)" 
                                        class="w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-xl transition flex items-center justify-center text-lg">
                                    +
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-add-cart">
                            Añadir al Carrito
                        </button>
                    </form>
                </div>

                <a href="{{ route('tienda.index') }}" class="back-action-link">← Volver al catálogo principal</a>
            </div>

        </div>
    </main>

    <!-- LÓGICA JAVASCRIPT DE LÍMITES POR TALLA -->
    <script>
        function actualizarLimiteStock(radio) {
            const stockMaximo = parseInt(radio.getAttribute('data-stock')) || 1;
            const inputCantidad = document.getElementById('cantidadInput');
            const textoMax = document.getElementById('textoMaxStock');

            // Actualiza el máximo permitido en el input
            inputCantidad.setAttribute('max', stockMaximo);

            // Si el valor actual supera el nuevo máximo, se reduce al límite
            if (parseInt(inputCantidad.value) > stockMaximo) {
                inputCantidad.value = stockMaximo;
            }

            // Muestra en texto el límite de la talla seleccionada
            textoMax.innerText = `Máx. ${stockMaximo} unidad(es) disponible(s)`;
        }

        function cambiarCantidad(monto) {
            const input = document.getElementById('cantidadInput');
            let valorActual = parseInt(input.value) || 1;
            let nuevoValor = valorActual + monto;

            const min = parseInt(input.min) || 1;
            const max = parseInt(input.getAttribute('max')) || 1;

            if (nuevoValor >= min && nuevoValor <= max) {
                input.value = nuevoValor;
            }
        }
    </script>

</body>
</html>