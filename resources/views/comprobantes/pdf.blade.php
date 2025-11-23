<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $venta->tipo_comprobante }} {{ $venta->numero_comprobante }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .empresa-info {
            font-size: 10px;
            color: #555;
            margin: 8px 0;
        }
        
        .comprobante-tipo {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
            margin: 8px 0;
        }
        
        .numero-comprobante {
            font-size: 14px;
            font-weight: bold;
            color: #34495e;
            margin-top: 5px;
        }
        
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-box {
            width: 48%;
            float: left;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-right: 2%;
            min-height: 120px;
        }
        
        .info-box:last-child {
            margin-right: 0;
        }
        
        .info-box h3 {
            font-size: 12px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-box p {
            margin: 5px 0;
            font-size: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .clearfix {
            clear: both;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #34495e;
            color: white;
        }
        
        table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #2c3e50;
        }
        
        table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totales-container {
            width: 100%;
            margin-top: 20px;
        }
        
        .totales {
            width: 45%;
            float: right;
        }
        
        .totales-qr-box {
            width: 53%;
            float: left;
        }

        .totales table {
            margin: 0;
            border: 1px solid #dee2e6;
        }
        
        .totales td {
            padding: 8px;
            font-size: 11px;
        }
        
        .totales .label-col {
            text-align: left;
            font-weight: bold;
            width: 60%;
        }
        
        .totales .value-col {
            text-align: right;
            width: 40%;
        }
        
        .totales .total-final {
            font-size: 13px;
            font-weight: bold;
            background-color: #2c3e50;
            color: white;
        }
        
        .observaciones {
            clear: both;
            margin-top: 30px;
            padding: 10px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            font-size: 10px;
        }
        
        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .hash-sunat {
            font-size: 8px;
            color: #6c757d;
            word-break: break-all;
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .estado-aceptado {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .estado-rechazado {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $empresa->getRazonSocial() }}</h1>
        <div class="empresa-info">
            RUC: {{ $empresa->getRuc() }}<br>
            {{ $empresa->getAddress()->getDireccion() }}<br>
            {{ $empresa->getAddress()->getDistrito() }} - {{ $empresa->getAddress()->getDepartamento() }}
        </div>
        <div class="comprobante-tipo">
            {{ $venta->tipo_comprobante === 'FACTURA' ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA' }}
        </div>
        <div class="numero-comprobante">{{ $venta->numero_comprobante }}</div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>DATOS DEL CLIENTE</h3>
            <p>
                <span class="info-label">
                    {{ $venta->tipo_comprobante === 'FACTURA' ? 'Razón Social:' : 'Nombre:' }}
                </span>
                {{ $cliente->nombre ?? 'Cliente General' }}
            </p>
            <p>
                <span class="info-label">{{ isset($cliente->documento) && strlen($cliente->documento) === 11 ? 'RUC:' : 'DNI:' }}</span>
                {{ $cliente->documento ?? 'N/A' }}
            </p>
            @if(isset($cliente->direccion) && !empty($cliente->direccion))
            <p>
                <span class="info-label">Dirección:</span>
                {{ $cliente->direccion }}
            </p>
            @endif
            @if(isset($cliente->telefono) && !empty($cliente->telefono))
            <p>
                <span class="info-label">Teléfono:</span>
                {{ $cliente->telefono }}
            </p>
            @endif
            @if(isset($cliente->email) && !empty($cliente->email))
            <p>
                <span class="info-label">Email:</span>
                {{ $cliente->email }}
            </p>
            @endif
        </div>
        
        <div class="info-box">
            <h3>DATOS DE LA VENTA</h3>
            <p>
                <span class="info-label">Fecha de Emisión:</span>
                {{ $venta->created_at->format('d/m/Y H:i') }}
            </p>
            <p>
                <span class="info-label">Tipo de Pago:</span>
                {{ $venta->tipo_pago === 'credito' ? 'Crédito' : 'Contado' }}
            </p>
            <p>
                <span class="info-label">Método de Pago:</span>
                @php
                    $metodoPago = [
                        'tarjeta' => 'Tarjeta de Crédito/Débito',
                        'stripe' => 'Tarjeta (Stripe)',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                        'transfer' => 'Transferencia Bancaria',
                        'delivery' => 'Pago contra entrega',
                        'efectivo' => 'Efectivo',
                    ];
                    $metodo = $venta->metodo_pago ?? 'efectivo';
                    echo $metodoPago[$metodo] ?? ucfirst($metodo);
                @endphp
            </p>
            @if($venta->numero_cuotas)
            <p>
                <span class="info-label">Número de Cuotas:</span>
                {{ $venta->numero_cuotas }}
            </p>
            <p>
                <span class="info-label">Monto por Cuota:</span>
                S/ {{ number_format($venta->monto_cuota ?? 0, 2) }}
            </p>
            @endif
        </div>
    </div>
    
    <div class="clearfix"></div>

    <h3 style="color: #2c3e50; margin-top: 20px; font-size: 12px;">DETALLE DE PRODUCTOS</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;" class="text-center">CANT.</th>
                <th style="width: 50%;">DESCRIPCIÓN</th>
                <th style="width: 20%;" class="text-right">P. UNIT.</th>
                <th style="width: 20%;" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
            <tr>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->producto->nombre }}</td>
                <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales-container">
        <div class="totales-qr-box">
            <div style="padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                <p style="font-size: 10px; margin-bottom: 10px;">
                    <strong>HASH SUNAT:</strong><br>
                    <span style="font-size: 8px; word-break: break-all;">{{ $hash_qr }}</span>
                </p>
                <p style="font-size: 9px; color: #6c757d; margin-top: 10px;">
                    Representación impresa de la {{ $venta->tipo_comprobante }} Electrónica
                </p>
            </div>
        </div>

        <div class="totales">
            <table>
                @php
                    $total = floatval($venta->total);
                    $subtotal = round($total / 1.18, 2);
                    $igv = round($total - $subtotal, 2);
                @endphp
                <tr>
                    <td class="label-col">Subtotal (Base Imponible):</td>
                    <td class="value-col">S/ {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">IGV (18%):</td>
                    <td class="value-col">S/ {{ number_format($igv, 2) }}</td>
                </tr>
                <tr class="total-final">
                    <td class="label-col">TOTAL:</td>
                    <td class="value-col">S/ {{ number_format($total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>

    @if($venta->observaciones)
    <div class="observaciones">
        <strong>Observaciones:</strong> {{ $venta->observaciones }}
    </div>
    @endif

    <div class="footer">
        <p><strong>Representación impresa de la {{ $venta->tipo_comprobante }} Electrónica</strong></p>
        
        @if($venta->estado_sunat)
        <p style="margin-top: 8px;">
            Estado SUNAT: 
            <span class="estado-badge {{ $venta->estado_sunat === 'ACEPTADO' ? 'estado-aceptado' : 'estado-rechazado' }}">
                {{ $venta->estado_sunat }}
            </span>
        </p>
        @endif
        
        @if($venta->hash_sunat)
        <div class="hash-sunat">
            <strong>CÓDIGO DE VERIFICACIÓN:</strong><br>
            {{ $venta->hash_sunat }}
        </div>
        @endif
        
        <p style="margin-top: 15px;">¡Gracias por su compra!</p>
    </div>
</body>
</html>