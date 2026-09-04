<?php

namespace App\Services\Chatbot\Exporters;

use App\Services\Chatbot\Contracts\ReportExporter;

// app/Services/Chatbot/Exporters/ReportExporterFactory.php

class ReportExporterFactory
{
    public static function make(string $format): ReportExporter
    {
        return match ($format) {
            'excel' => new ExcelExporter(),
            'txt'   => new TxtExporter(),
            default => new PdfExporter(),
        };
    }
}
