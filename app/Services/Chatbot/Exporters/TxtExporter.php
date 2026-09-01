<?php

namespace App\Services\Chatbot\Exporters;

use App\Services\Chatbot\Contracts\ReportExporter;
use Illuminate\Support\Facades\Storage;

// app/Services/Chatbot/Exporters/TxtExporter.php
// no necesita ninguna librería nueva — el "bloc de notas" es simplemente texto plano

class TxtExporter implements ReportExporter
{
    public function export(string $title, array $rows): string
    {
        $lines = [$title, str_repeat('-', strlen($title)), ''];
        foreach ($rows as $row) {
            $lines[] = collect($row)->map(fn($v, $k): string => sprintf('%s: %s', $k, $v))->implode(' | ');
        }

        $path = 'reportes/' . \Illuminate\Support\Str::uuid() . '.txt';
        Storage::disk('local')->put($path, implode(PHP_EOL, $lines));

        return $path;
    }

    public function extension(): string
    {
        return 'txt';
    }
}
