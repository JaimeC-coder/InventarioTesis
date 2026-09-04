<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

// app/Exports/ChatbotReportExport.php

class ChatbotReportExport implements FromArray, WithHeadings
{
    public function __construct(private array $rows)
    {
    }

    public function array(): array
    {
        return collect($this->rows)->map(fn($row) => array_values($row))->all();
    }

    public function headings(): array
    {
        return array_keys($this->rows[0] ?? []);
    }
}
