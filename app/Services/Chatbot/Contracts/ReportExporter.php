<?php

// app/Services/Chatbot/Contracts/LlmClient.php

namespace App\Services\Chatbot\Contracts;

// app/Services/Chatbot/Contracts/ReportExporter.php

interface ReportExporter
{
    public function export(string $title, array $rows): string; // devuelve la ruta del archivo generado

    public function extension(): string;
}
