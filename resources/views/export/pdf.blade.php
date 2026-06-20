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

    <h2>Reporte de {{ $titulo }} </h2>

    <table>
        <thead>
            <tr>
                <th width="10"> # </th>
               @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach ($items as $item => $value)
                <tr>
                    <td >{{ $item + 1 }}</td>
                    @foreach ($columns as $column)
                        <td>{{ $value->$column ?? '---' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
