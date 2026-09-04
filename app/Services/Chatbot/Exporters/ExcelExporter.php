<?php

namespace App\Services\Chatbot\Exporters;

use App\Exports\ChatbotReportExport;
use App\Services\Chatbot\Contracts\ReportExporter;
use Maatwebsite\Excel\Facades\Excel;

// app/Services/Chatbot/Exporters/ExcelExporter.php
// requiere: composer require maatwebsite/excel

class ExcelExporter implements ReportExporter
{
    public function export(string $title, array $rows): string
    {
        $path = 'reportes/' . \Illuminate\Support\Str::uuid() . '.xlsx';
        Excel::store(new ChatbotReportExport($rows), $path, 'local');

        return $path;
    }

    public function extension(): string
    {
        return 'xlsx';
    }
}
