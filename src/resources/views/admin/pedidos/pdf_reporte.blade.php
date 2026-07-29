<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos - Sneakers LH</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 8px;
        }
        .header table { width: 100%; }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11px;
            color: #6b7280;
        }
        .badge-rango {
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }
        .order-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .order-header {
            background-color: #f9fafb;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: bold;
        }
        .order-body { padding: 10px 12px; }
        .cliente-info {
            width: 100%;
            margin-bottom: 10px;
            background-color: #f3f4f6;
            padding: 8px;
            border-radius: 6px;
            border-collapse: collapse;
        }
        .cliente-info td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .items-table th, .items-table td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
        }
        .items-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
        }
        .img-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* CUADRO DE RESUMEN AL FINAL */
        .total-pedidos-box {
            margin-top: 20px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .total-pedidos-box table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO DEL REPORTE -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">SNEAKERS LH</div>
                    <div class="subtitle">Reporte Consolidado de Ventas y Pedidos</div>
                </td>
                <td class="text-right">
                    <div class="badge-rango">
                        Rango: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                    </div>
                    <br><small style="color: #9ca3af;">Generado el: {{ date('d/m/Y H:i') }}</small>
                </td>
            </tr>
        </table>
    </div>

    @php 
        $montoPagadoTotal = 0;
        $montoCanceladoTotal = 0;
        $montoTotalGeneral = 0;
    @endphp

    @forelse($pedidos as $pedido)
        @php 
            $montoPedido = $pedido->total ?? $pedido->monto_total ?? 0;
            $montoTotalGeneral += $montoPedido;

            $estadoStr = strtolower(trim($pedido->estado ?? $pedido->estado_pedido ?? 'pendiente'));
            $esConfirmado = !empty($pedido->venta_id) || in_array($estadoStr, ['confirmado', 'pagado', 'aprobado', 'completado']);
            $esCancelado  = in_array($estadoStr, ['cancelado', 'rechazado']);

            if ($esConfirmado) {
                $montoPagadoTotal += $montoPedido;
            } elseif ($esCancelado) {
                $montoCanceladoTotal += $montoPedido;
            }

            $usuario = $pedido->usuario ?? null;
            $nombreCliente = trim(($usuario->nombre ?? $usuario->name ?? $pedido->user_nombre ?? '') . ' ' . ($usuario->apellido ?? $pedido->user_apellido ?? ''));
            if (empty($nombreCliente)) { $nombreCliente = 'Cliente Registrado'; }
            
            $cedula = $pedido->cedula ?? $usuario->cedula ?? 'No especificada';
            $email  = $usuario->email ?? $pedido->user_email ?? 'N/A';
            $telefono = $pedido->telefono_final ?? $pedido->telefono ?? $usuario->telefono ?? 'N/A';
            
            $depto = $pedido->departamento ?? 'No especificado';
            $ciudad = $pedido->ciudad ?? 'No especificada';
            $barrio = $pedido->barrio ?? 'No especificado';
            $direccion = $pedido->direccion ?? $usuario->direccion ?? 'Entrega Local / Sin Especificar';
            $indicaciones = $pedido->indicaciones ?? '';
        @endphp
        
        <div class="order-card">
            <div class="order-header">
                <table style="width: 100%;">
                    <tr>
                        <td>PEDIDO #{{ $pedido->id }} — Fecha: {{ \Carbon\Carbon::parse($pedido->created_at ?? $pedido->fecha)->format('d/m/Y h:i A') }}</td>
                        <td class="text-right">
                            Estado: 
                            @if($esConfirmado)
                                <strong style="color: #059669;">CONFIRMADO / PAGADO</strong>
                            @elseif($esCancelado)
                                <strong style="color: #dc2626;">CANCELADO</strong>
                            @else
                                <strong style="color: #d97706;">PENDIENTE</strong>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="order-body">
                <!-- DATOS DEL CLIENTE Y UBICACIÓN DE ENVÍO -->
                <table class="cliente-info">
                    <tr>
                        <td style="width: 33%;"><strong>Cliente:</strong> {{ $nombreCliente }}</td>
                        <td style="width: 33%;"><strong>Cédula / CC:</strong> {{ $cedula }}</td>
                        <td style="width: 34%;"><strong>Correo:</strong> {{ $email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong> {{ $telefono }}</td>
                        <td colspan="2">
                            <strong>Ubicación:</strong> {{ $depto }} — {{ $ciudad }} @if(!empty($barrio) && $barrio !== 'No especificado') (Barrio: {{ $barrio }}) @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong>Dirección Exacta de Envío:</strong> {{ $direccion }}
                            @if(!empty($indicaciones) && $indicaciones !== 'Sin observaciones')
                                <br><span style="color: #4b5563;"><strong>Indicaciones de Entrega:</strong> {{ $indicaciones }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- DETALLE DE PRODUCTOS -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">Foto</th>
                            <th>Producto</th>
                            <th class="text-center">Talla</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-right">P. Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $detalles = $pedido->detalles ?? []; @endphp
                        @forelse($detalles as $detalle)
                            @php
                                $det = (object) $detalle;
                                $producto = $det->producto ?? null;
                                $prodNombre = $det->producto_nombre ?? $producto->nombre ?? 'Producto #' . ($det->producto_id ?? '');
                                $prodImagen = $det->producto_imagen ?? $producto->imagen_url ?? $producto->imagen ?? null;
                                $tallaNum   = $det->talla ?? $det->numero ?? 'N/A';
                                $cantNum    = $det->cantidad ?? $det->cant ?? 1;
                                $precioUnit = $det->precio_unitario ?? $det->precio ?? $producto->precio ?? 0;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($prodImagen)
                                        <img src="{{ $prodImagen }}" class="img-thumb" alt="Zapato">
                                    @else
                                        <span>👟</span>
                                    @endif
                                </td>
                                <td><strong>{{ $prodNombre }}</strong></td>
                                <td class="text-center">{{ $tallaNum }}</td>
                                <td class="text-center">{{ $cantNum }}</td>
                                <td class="text-right">$ {{ number_format($precioUnit, 0, ',', '.') }}</td>
                                <td class="text-right">$ {{ number_format($cantNum * $precioUnit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="color: #9ca3af;">Sin desglose detallado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-right" style="margin-top: 8px; font-size: 12px;">
                    <strong>Total del Pedido: 
                        <span style="color: {{ $esCancelado ? '#dc2626' : '#10b981' }};">
                            $ {{ number_format($montoPedido, 0, ',', '.') }}
                        </span>
                    </strong>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <h3>No se encontraron pedidos ni compras en el rango de fechas seleccionado.</h3>
        </div>
    @endforelse

    <!-- RESUMEN EN TABLA DE 4 COLUMNAS AL FINAL DEL PDF -->
    @if(count($pedidos) > 0)
        <div class="total-pedidos-box">
            <table>
                <tr>
                    <td style="width: 22%; text-align: left; border-right: 1px solid #e2e8f0;">
                        <span style="color: #64748b; font-size: 9px; font-weight: bold; text-transform: uppercase; display: block;">Pedidos Encontrados</span>
                        <strong style="font-size: 14px; color: #1e293b;">{{ count($pedidos) }} Registros</strong>
                    </td>
                    <td style="width: 26%; text-align: center; border-right: 1px solid #e2e8f0;">
                        <span style="color: #059669; font-size: 9px; font-weight: bold; text-transform: uppercase; display: block;">Monto Acumulado Pagado</span>
                        <strong style="font-size: 13px; color: #059669;">$ {{ number_format($montoPagadoTotal, 0, ',', '.') }}</strong>
                    </td>
                    <td style="width: 26%; text-align: center; border-right: 1px solid #e2e8f0;">
                        <span style="color: #dc2626; font-size: 9px; font-weight: bold; text-transform: uppercase; display: block;">Monto Acumulado Cancelado</span>
                        <strong style="font-size: 13px; color: #dc2626;">$ {{ number_format($montoCanceladoTotal, 0, ',', '.') }}</strong>
                    </td>
                    <td style="width: 26%; text-align: right; padding-left: 8px;">
                        <span style="color: #4338ca; font-size: 9px; font-weight: bold; text-transform: uppercase; display: block;">Monto Acumulado Total</span>
                        <strong style="font-size: 14px; color: #4f46e5;">$ {{ number_format($montoTotalGeneral, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>
    @endif

</body>
</html>