<?php

namespace App\Services\Chatbot;

use App\Services\Chatbot\Exporters\ReportExporterFactory;

class ReportExportService
{
    public function export(string $format, string $title, array $rows): array
    {
        $reportExporter = ReportExporterFactory::make($format);
        $path = $reportExporter->export($title, $rows);

        return [
            'path' => $path,
            'extension' => $reportExporter->extension(),
        ];
    }
}
