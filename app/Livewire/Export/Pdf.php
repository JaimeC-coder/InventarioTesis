<?php

namespace App\Livewire\Export;

use Barryvdh\DomPDF\Facade\Pdf as domPdf;
use Livewire\Component;

class Pdf extends Component
{
    #[\Livewire\Attributes\On('openPdfExport')]
    public function generatePdfNow(array $payload)
    {
        $model = $payload['model'];
        $uuids = $payload['uuids'];
        // Buscar datos
        $items = $model::whereIn('uuid', $uuids)->get();
        // Crear PDF
        $pdf = domPdf::loadView('export.pdf', [
            'items' => $items,
            'data' => $items,
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->stream();
        }, 'reporte.pdf');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.export.pdf');
    }
}
