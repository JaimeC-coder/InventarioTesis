<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura Electrónica</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .company {
            font-size: 12px;
            font-weight: bold;
        }

        .company small {
            font-weight: normal;
            display: block;
            margin-top: 3px;
        }

        .invoice-box {
            border: 1px solid #000;
            text-align: center;
            padding: 8px;
        }

        .invoice-box strong {
            font-size: 13px;
        }

        .info {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 4px;
            vertical-align: top;
        }

        .info .label {
            font-weight: bold;
            width: 120px;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.details th,
        table.details td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10px;
        }

        table.details th {
            background-color: #f2f2f2;
            text-align: center;
        }

        table.details td {
            text-align: center;
        }

        table.details td.description {
            text-align: left;
        }

        .totals {
            width: 100%;
            margin-top: 10px;
        }

        .totals table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }

        .totals td {
            padding: 4px;
            border: 1px solid #000;
        }

        .totals .label {
            font-weight: bold;
            text-align: right;
        }

        .totals .amount {
            text-align: right;
        }

        .amount-text {
            margin-top: 15px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>

<body>
<div class="container">

    <!-- ENCABEZADO -->
    <div class="header">
        <table>
            <tr>
                <td width="70%">
                    <div class="company">
                        {{ $propietario['name'] ?? 'INVERSIÓNES ISABEL' }}
                        <small>{{ $propietario['address_specific'] ?? 'ANCÓN - LIMA' }}</small>
                        <small>RUC: {{ $propietario['document_number'] ?? '10192555685' }}</small>
                    </div>
                </td>
                <td width="30%">
                    <div class="invoice-box">
                        <strong>FACTURA ELECTRÓNICA</strong><br>
                        RUC: {{ $propietario['document_number'] ?? '10192555685' }}<br>
                        {{ $description_general['serie'] ?? 'E001' }} - {{ $description_general['correlativo'] ?? '1572' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- DATOS CLIENTE -->
    <div class="info">
        <table>
            <tr>
                <td class="label">Cliente:</td>
                <td>{{ $client['name'] ?? 'INVERSIÓNES MODROM´S S.A.C' }}</td>

                <td class="label">Fecha:</td>
                <td>{{ $description_general['date'] ?? date('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ $client['identity'] ?? '' }}:</td>
                <td>{{ $client['document_number'] ?? '20545856660' }}</td>
                <td class="label">Moneda:</td>
                <td>SOLES</td>
            </tr>
            <tr>
                <td class="label">Dirección:</td>
                <td colspan="3">{{ $client['address'] ?? 'SAN JUAN DE MIRAFLORES - LIMA' }}</td>
            </tr>
        </table>
    </div>

    <!-- DETALLE -->
    <table class="details">
        <thead>
        <tr>
            <th>#</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Código</th>
            <th>Descripción</th>
            <th>V. Unitario</th>
            <th>Total</th>
            <th>ICBPER</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $i => $value)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ number_format($value->pivot->quantity ?? 0, 2) }}</td>
                <td>{{ $value->unit->name ?? 'UND' }}</td>
                <td>{{ $value->barcode ?? '---' }}</td>
                <td class="description">{{ $value->name ?? '---' }}</td>
                <td>{{ number_format($value->pivot->price ?? 0, 2) }}</td>
                <td>{{ number_format($value->pivot->subtotal ?? 0, 2) }}</td>
                <td>0.00</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totals">
        <table>
            <tr>
                <td class="label">Sub Total</td>
                <td class="amount">S/ {{ number_format($description_general['subtotal'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="label">IGV (18%)</td>
                <td class="amount">S/ {{ number_format($description_general['igv'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Importe Total</td>
                <td class="amount"><strong>S/ {{ number_format($description_general['total'] ?? 0, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <!-- MONTO EN LETRAS -->
    <div class="amount-text">
        SON: {{ $description_general['total_string'] ?? 'OCHO MIL DOSCIENTOS SETENTA Y NUEVE Y 50/100 SOLES' }}
    </div>

    <!-- PIE -->
    <div class="footer">
        Esta es una representación impresa de la factura electrónica, generada en el sistema de SUNAT.
    </div>

</div>
</body>
</html>
