<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte Exportado</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>Reporte de {{ $data  }} </h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($items as $item => $value)
                <tr>
                    <td>{{ $item + 1 }}</td>
                    <td>{{ $value->name ?? '---' }}</td>
                    <td>{{ $value->description ?? '---' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
