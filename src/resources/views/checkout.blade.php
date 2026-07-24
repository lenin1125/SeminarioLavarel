<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Compra - SneakersLH</title>
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
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 0 20px;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #111827;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 30px;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            font-size: 22px;
            font-weight: 800;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .badge-sim {
            background: rgba(99, 102, 241, 0.1);
            color: #818cf8;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 25px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            display: inline-block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px 16px;
            background: #0b0f17;
            border: 1px solid #374151;
            border-radius: 8px;
            font-size: 14px;
            color: white;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #6366f1;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #1f2937;
        }

        .product-name {
            font-weight: 700;
            font-size: 15px;
        }

        .product-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .product-price {
            font-weight: 700;
            color: #ffffff;
        }

        .total-box {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #1f2937;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-box span {
            font-size: 18px;
            font-weight: 700;
        }

        .total-price {
            color: #6366f1;
            font-size: 24px;
            font-weight: 800;
        }

        .btn-submit {
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
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #4f46e5;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <a href="{{ route('tienda.index') }}" class="logo-box">
            <img src="/logo.jpg" alt="Logo" class="logo-img">
            <div style="margin-left: 12px;">
                <div class="logo-title">SneakersLH</div>
            </div>
        </a>
    </header>

    <div class="container">
        
        <!-- RESUMEN EN EL MISMO ESTILO OSCURO -->
        <div class="panel">
            <h2>Resumen de Compra</h2>
            
            @php $totalCheckout = 0; @endphp
            @foreach($carrito as $item)
                @php $totalCheckout += $item['precio'] * $item['cantidad']; @endphp
                <div class="product-item">
                    <div>
                        <div class="product-name">{{ $item['nombre'] }}</div>
                        <div class="product-meta">Talla: {{ $item['talla'] }} | Cantidad: {{ $item['cantidad'] }}</div>
                    </div>
                    <div class="product-price">
                        ${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
            
            <div class="total-box">
                <span>Total a facturar:</span>
                <span class="total-price">${{ number_format($totalCheckout, 0, ',', '.') }} COP</span>
            </div>
        </div>

        <!-- FORMULARIO CON DATOS DE ENVÍO -->
        <div class="panel">
            <h2>Datos de Envío & Facturación</h2>
            <div class="badge-sim">🔒 Transacción Simulada Segura</div>
            
            <form action="{{ route('checkout.procesar') }}" method="POST">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Primer nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Apellidos" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cedula">Cédula de Ciudadanía *</label>
                        <input type="text" id="cedula" name="cedula" placeholder="Número de documento" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Número de Celular / WhatsApp *</label>
                        <input type="text" id="telefono" name="telefono" placeholder="Ej. 310500000" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email ?? '' }}" placeholder="nombre@correo.com" required>
                </div>

                <!-- SECCIÓN DE DIRECCIÓN DE ENVÍO -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="departamento">Departamento *</label>
                        <input type="text" id="departamento" name="departamento" placeholder="Ej. Valle del Cauca" required>
                    </div>

                    <div class="form-group">
                        <label for="ciudad">Ciudad / Municipio *</label>
                        <input type="text" id="ciudad" name="ciudad" placeholder="Ej. Cali" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección de Residencia *</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ej. Calle 10 # 5 - 20" required>
                </div>

                <div class="form-group">
                    <label for="barrio_notas">Barrio o Indicaciones de Entrega (Opcional)</label>
                    <input type="text" id="barrio_notas" name="barrio_notas" placeholder="Ej. Barrio Granada, Apto 201">
                </div>
                
                <button type="submit" class="btn-submit">
                    Finalizar Compra y Generar Factura
                </button>
            </form>
            
            <a href="{{ route('carrito.index') }}" class="back-link">Volver a revisar el carrito</a>
        </div>

    </div>

</body>
</html>