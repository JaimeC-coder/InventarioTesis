<html>
<body style="font-family: sans-serif; font-size: 12px;">
    <h2>{{ $title }}</h2>
    <p style="color: #888;">Generado el {{ $generatedAt }}</p>
    <table style="width: 100%; border-collapse: collapse;">
        @foreach ($rows as $row)
            <tr style="border-bottom: 1px solid #eee;">
                @foreach ($row as $label => $value)
                    <td style="padding: 6px 0;">{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
