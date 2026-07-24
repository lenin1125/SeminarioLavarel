<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adjuntar Pago - SneakersLH</title>
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

        main {
            max-width: 600px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .panel {
            background: #111827;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 40px;
            text-align: center;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #9ca3af;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .payment-instructions {
            background: #0b0f17;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .instruction-title {
            font-size: 12px;
            font-weight: 700;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .account-detail {
            font-size: 15px;
            margin: 8px 0;
            color: #e5e7eb;
        }

        .account-detail strong {
            color: #ffffff;
        }

        .file-dropzone {
            border: 2px dashed #374151;
            padding: 30px 20px;
            border-radius: 12px;
            background: #0b0f17;
            cursor: pointer;
            transition: border-color 0.2s;
            margin-bottom: 25px;
        }

        .file-dropzone:hover {
            border-color: #6366f1;
        }

        .file-dropzone input[type="file"] {
            display: block;
            margin: 10px auto 0 auto;
            color: #9ca3af;
            font-size: 14px;
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
        }

        .btn-submit:hover {
            background: #4f46e5;
        }

        .alert-danger {
            background-color: #ef444422;
            border: 1px solid #ef4444;
            color: #f87171;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <header>
        <a href="{{ route('tienda.index') }}" class="logo-box">
            <img src="/logo.jpg" alt="Logo" class="logo-img">
            <div>
                <div class="logo-title">SneakersLH</div>
            </div>
        </a>
    </header>

    <main>
        <div class="panel">
            <h2>¡Pedido Registrado con Éxito!</h2>
            <div class="subtitle">Completa tu pago para procesar el envío de tus tenis</div>

            @if(session('error'))
                <div class="alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- INSTRUCCIONES DE TRANSFERENCIA -->
            <div class="payment-instructions">
                <div class="instruction-title">Cuentas Disponibles</div>
                <div class="account-detail">📱 <strong>Nequi / Daviplata:</strong> 318 525 2717</div>
                <div class="account-detail">🏦 <strong>Ahorros Bancolombia:</strong> 123-456789-01</div>
                <p style="font-size: 13px; color: #9ca3af; margin-top: 15px; margin-bottom: 0;">
                    Realiza la transferencia por el total indicado y sube una captura o documento del comprobante abajo.
                </p>
            </div>

            <!-- FORMULARIO CARGA COMPROBANTE -->
            <form action="{{ route('checkout.guardar_pago') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- ID DEL PEDIDO OBLIGATORIO -->
                <input type="hidden" name="pedido_id" value="{{ $pedidoId }}">

                <div class="file-dropzone">
                    <div style="font-size: 30px; margin-bottom: 10px;">📄 / 📸</div>
                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 5px;">Selecciona tu Comprobante</div>
                    <div style="font-size: 12px; color: #9ca3af;">Formatos permitidos: JPG, PNG, PDF (Máx. 5MB)</div>
                    
                    <!-- UNICO INPUT CORREGIDO -->
                    <input type="file" name="comprobante" accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                </div>

                <button type="submit" class="btn-submit">
                    Enviar Comprobante y Finalizar
                </button>
            </form>
        </div>
    </main>

</body>
</html>