<html>
<body style="font-family: sans-serif; font-size: 12px;">
    <h2>{{ $title }}</h2>
    <p style="color: #888;">Generado el {{ $generatedAt }}</p>
    <table style="width: 100%; border-collapse: collapse;">
        @if (!empty($rows))
            <thead>
                <tr style="border-bottom: 2px solid #333;">
                    @foreach (array_keys($rows[0] ?? []) as $header)
                        <th style="padding: 6px 0; text-align: left;">{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            @foreach ($rows as $row)
                <tr style="border-bottom: 1px solid #eee;">
                    @foreach ($row as $label => $value)
                        <td style="padding: 6px 0;">{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
