<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Reparación #{{ $reparacion->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-row strong {
            display: inline-block;
            width: 150px;
            color: #555;
        }
        .costos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .costos-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .costos-table .label {
            font-weight: bold;
            color: #555;
        }
        .costos-table .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .estado-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .estado-recibido { background-color: #e3f2fd; color: #1976d2; }
        .estado-diagnosticando { background-color: #fff3e0; color: #f57c00; }
        .estado-en_reparacion { background-color: #fff9c4; color: #f9a825; }
        .estado-esperando_repuestos { background-color: #fce4ec; color: #c2185b; }
        .estado-listo { background-color: #c8e6c9; color: #388e3c; }
        .estado-entregado { background-color: #b2dfdb; color: #00796b; }
        .estado-cancelado { background-color: #ffcdd2; color: #d32f2f; }
    </style>
</head>
<body>
    <div class="header">
        <h1>COMPROBANTE DE REPARACIÓN</h1>
        <p>N° {{ str_pad($reparacion->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <h3>Información del Cliente</h3>
        <div class="info-row">
            <strong>Nombre:</strong> {{ $reparacion->cliente_nombre }}
        </div>
        <div class="info-row">
            <strong>Teléfono:</strong> {{ $reparacion->cliente_telefono ?? 'No proporcionado' }}
        </div>
    </div>

    <div class="info-box">
        <h3>Información del Equipo</h3>
        <div class="info-row">
            <strong>Tipo de equipo:</strong> {{ $reparacion->tipo_equipo }}
        </div>
        <div class="info-row">
            <strong>Marca:</strong> {{ $reparacion->marca ?? 'No especificada' }}
        </div>
        <div class="info-row">
            <strong>Modelo:</strong> {{ $reparacion->modelo ?? 'No especificado' }}
        </div>
        <div class="info-row">
            <strong>Número de serie:</strong> {{ $reparacion->numero_serie ?? 'No disponible' }}
        </div>
    </div>

    <div class="info-box">
        <h3>Diagnóstico y Reparación</h3>
        <div class="info-row">
            <strong>Problema reportado:</strong><br>
            {{ $reparacion->problema_reportado ?? 'No especificado' }}
        </div>
        <div class="info-row" style="margin-top: 8px;">
            <strong>Diagnóstico:</strong><br>
            {{ $reparacion->diagnostico ?? 'Pendiente' }}
        </div>
        <div class="info-row" style="margin-top: 8px;">
            <strong>Solución aplicada:</strong><br>
            {{ $reparacion->solucion_aplicada ?? 'Pendiente' }}
        </div>
    </div>

    <div class="info-box">
        <h3>Costos</h3>
        <table class="costos-table">
            <tr>
                <td class="label">Mano de obra:</td>
                <td style="text-align: right;">S/ {{ number_format($reparacion->costo_mano_obra, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Repuestos:</td>
                <td style="text-align: right;">S/ {{ number_format($reparacion->costo_repuestos, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">TOTAL:</td>
                <td style="text-align: right;">S/ {{ number_format($reparacion->costo_total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <h3>Estado y Fechas</h3>
        <div class="info-row">
            <strong>Estado:</strong> 
            <span class="estado-badge estado-{{ $reparacion->estado }}">
                {{ strtoupper(str_replace('_', ' ', $reparacion->estado)) }}
            </span>
        </div>
        <div class="info-row">
            <strong>Prioridad:</strong> {{ strtoupper($reparacion->prioridad ?? 'normal') }}
        </div>
        <div class="info-row">
            <strong>Fecha de ingreso:</strong> {{ $reparacion->fecha_ingreso?->format('d/m/Y') ?? 'No registrada' }}
        </div>
        <div class="info-row">
            <strong>Fecha estimada de entrega:</strong> {{ $reparacion->fecha_estimada_entrega?->format('d/m/Y') ?? 'No definida' }}
        </div>
        @if($reparacion->fecha_entrega_real)
        <div class="info-row">
            <strong>Fecha de entrega real:</strong> {{ $reparacion->fecha_entrega_real->format('d/m/Y') }}
        </div>
        @endif
        @if($reparacion->calificacion)
        <div class="info-row">
            <strong>Calificación del servicio:</strong> {{ $reparacion->calificacion }}/5 ⭐
        </div>
        @endif
    </div>

    @if($reparacion->notas_internas)
    <div class="info-box">
        <h3>Notas Internas</h3>
        <p style="font-size: 11px; color: #666;">{{ $reparacion->notas_internas }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Este documento es un comprobante de servicio técnico</p>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>