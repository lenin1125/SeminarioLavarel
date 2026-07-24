<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Compra {{ $compra['n_factura'] }} - SneakersLH</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            color: #374151;
        }
        .invoice-card {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.05);
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px dashed #e5e7eb;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }
        .brand h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: #111827;
        }
        .brand p {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #6b7280;
        }
        .meta-info {
            text-align: right;
        }
        .meta-info h3 {
            margin: 0;
            font-size: 16px;
            color: #4f46e5;
            font-weight: 700;
        }
        .meta-info p {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #6b7280;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .client-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 14px;
            line-height: 1.6;
        }
        .client-details div {
            margin-bottom: 4px;
        }
        .client-details strong {
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            text-align: left;
            padding: 12px 8px;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 14px 8px;
            font-size: 14px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-top: 10px;
        }
        .total-amount {
            color: #4f46e5;
            margin-left: 15px;
        }
        .status-badge {
            background-color: #d1fae5;
            color: #065f46;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            display: inline-block;
            margin-top: 25px;
        }
        .actions-box {
            margin-top: 35px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
            border: none;
        }
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        .btn-primary:hover {
            background: #4338ca;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        @media print {
            body { background: white; padding: 0; }
            .invoice-card { box-shadow: none; padding: 0; max-width: 100%; }
            .actions-box { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    
    <!-- ENCABEZADO DE LA FACTURA -->
    <div class="header">
        <div class="brand">
            <h1>SneakersLH</h1>
            <p>Tienda Virtual de Calzado Exclusivo</p>
        </div>
        <div class="meta-info">
            <h3>FACTURA COMERCIAL</h3>
            <p><strong>N°:</strong> {{ $compra['n_factura'] }}</p>
            <p><strong>Fecha:</strong> {{ $compra['fecha'] }}</p>
        </div>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section-title">Información de Facturación</div>
    <div class="client-details">
        <div><strong>Cliente:</strong> {{ $compra['cliente']['nombre'] }} {{ $compra['cliente']['apellido'] }}</div>
        <div><strong>Cédula / Documento:</strong> {{ $compra['cliente']['cedula'] }}</div>
        <div><strong>Teléfono:</strong> {{ $compra['cliente']['telefono'] }}</div>
        <div><strong>Email:</strong> {{ $compra['cliente']['email'] }}</div>
    </div>

    <!-- DETALLE DE PRODUCTOS -->
    <div class="section-title">Resumen de la Compra</div>
    <table>
        <thead>
            <tr>
                <th>Descripción del Modelo</th>
                <th class="text-center">Talla</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAcumulado = 0; @endphp
            @foreach($compra['productos'] as $item)
                @php 
                    $subtotalItem = $item['precio'] * $item['cantidad'];
                    $totalAcumulado += $subtotalItem;
                @endphp
                <tr>
                    <td>{{ $item['nombre'] }}</td>
                    <td class="text-center">{{ $item['talla'] }}</td>
                    <td class="text-center">{{ $item['cantidad'] }}</td>
                    <td class="text-right">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($subtotalItem, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL GENERAL -->
    <div class="total-row">
        <span>Total Pagado:</span>
        <span class="total-amount">${{ number_format($totalAcumulado, 0, ',', '.') }} COP</span>
    </div>

    <!-- ESTADO DE LA TRANSACCIÓN -->
    <div class="text-center">
        <span class="status-badge">✓ Transacción Aprobada Exitosamente</span>
    </div>

    <!-- ACCIONES -->
    <div class="actions-box">
        <button onclick="window.print();" class="btn btn-secondary">Imprimir Recibo</button>
        <a href="{{ route('tienda.index') }}" class="btn btn-primary">Volver al Inicio</a>
    </div>

</div>

</body>
</html>