<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - SneakersLH</title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #0b0f17;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

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
            border-radius: 8px;
        }

        .logo-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
        }

        .logo-subtitle {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            margin-top: 2px;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 30px;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 15px;
        }

        /* Alertas */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-error {
            background-color: #7f1d1d;
            color: #fca5a5;
            border: 1px solid #991b1b;
        }
        .alert-success {
            background-color: #064e3b;
            color: #6ee7b7;
            border: 1px solid #065f46;
        }

        .cart-container {
            background: #111827;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 10px;
            color: #9ca3af;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1f2937;
        }

        td {
            padding: 20px 10px;
            border-bottom: 1px solid #1f2937;
            vertical-align: middle;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-info img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            background: #1f2937;
        }

        .product-name {
            font-weight: 700;
            font-size: 16px;
        }

        .product-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .stock-info {
            font-size: 11px;
            color: #10b981;
            margin-top: 2px;
            font-weight: 600;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-qty {
            background: #1f2937;
            border: 1px solid #374151;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, opacity 0.2s;
        }

        .btn-qty:hover:not(:disabled) {
            background: #374151;
        }

        .btn-qty:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #111827;
        }

        .btn-delete {
            background: transparent;
            border: none;
            color: #f87171;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }

        .price-text {
            font-weight: 700;
            font-size: 16px;
        }

        .cart-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #1f2937;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 18px;
            color: #9ca3af;
            font-weight: 600;
        }

        .total-amount {
            font-size: 26px;
            font-weight: 800;
            color: #6366f1;
        }

        .actions-row {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .btn-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-link:hover {
            color: white;
        }

        .btn-main {
            padding: 14px 30px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-block;
        }

        .btn-main:hover {
            background: #4f46e5;
        }

        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <header>
        <a href="{{ route('tienda.index') }}" class="logo-box">
            <img src="/logo.jpg" alt="Logo" class="logo-img">
            <div>
                <div class="logo-title">SneakersLH</div>
                <div class="logo-subtitle">Tu estilo, a cada paso</div>
            </div>
        </a>
    </header>

    <main>
        <h2>Tu Carrito de Compras</h2>

        {{-- Alertas de Mensajes del Servidor --}}
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="cart-container">
            @if(count($carrito) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalGeneral = 0; @endphp
                        @foreach($carrito as $key => $item)
                            @php 
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $totalGeneral += $subtotal;
                                $maxStock = $item['max_stock'] ?? 10;
                            @endphp
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="{{ $item['imagen_url'] ?? '/logo.jpg' }}" alt="{{ $item['nombre'] }}">
                                        <div>
                                            <div class="product-name">{{ $item['nombre'] }}</div>
                                            <div class="product-meta">Talla: {{ $item['talla'] }}</div>
                                            <div class="stock-info">Disponible: {{ $maxStock }} uds.</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="price-text">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                                <td>
                                    <div class="quantity-control">
                                        <form action="{{ route('carrito.actualizar', $key) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="accion" value="decrementar">
                                            <button type="submit" class="btn-qty">-</button>
                                        </form>
                                        
                                        <span style="font-weight: 700; width: 20px; text-align: center;">{{ $item['cantidad'] }}</span>
                                        
                                        <form action="{{ route('carrito.actualizar', $key) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="accion" value="incrementar">
                                            <button type="submit" class="btn-qty" {{ $item['cantidad'] >= $maxStock ? 'disabled' : '' }}>+</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="price-text" style="color: #6366f1;">${{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('carrito.actualizar', $key) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn-delete">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="cart-summary">
                    <span class="total-label">Total del Pedido:</span>
                    <span class="total-amount">${{ number_format($totalGeneral, 0, ',', '.') }} COP</span>
                </div>

                <div class="actions-row">
                    <a href="{{ route('tienda.index') }}" class="btn-link">← Continuar comprando</a>
                    <a href="{{ route('checkout.index') }}" class="btn-main">Proceder al Pago</a>
                </div>
            @else
                <div class="empty-cart">
                    <p style="font-size: 18px; margin-bottom: 20px;">No tienes ningún modelo agregado al carrito todavía.</p>
                    <a href="{{ route('tienda.index') }}" class="btn-main">Volver al catálogo</a>
                </div>
            @endif
        </div>
    </main>

</body>
</html>