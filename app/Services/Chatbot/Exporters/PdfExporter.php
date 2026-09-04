<?php

namespace App\Services\Chatbot\Exporters;

use App\Services\Chatbot\Contracts\ReportExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

// app/Services/Chatbot/Exporters/PdfExporter.php

class PdfExporter implements ReportExporter
{
    public function export(string $title, array $rows): string
    {
        $pdf = Pdf::loadView('export.chatbot-export', [
            'title' => $title,
            'rows' => $rows,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);
        $path = 'reportes/' . \Illuminate\Support\Str::uuid() . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    public function extension(): string
    {
        return 'pdf';
    }
}
