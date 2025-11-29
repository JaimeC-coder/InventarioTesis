<?php

namespace App\Livewire\Export;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf as domPdf;


class Pdf extends Component
{

    #[\Livewire\Attributes\On('openPdfExport')]
    public function generatePdfNow($payload)
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

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte.pdf');
    }


    public function render()
    {
        return view('livewire.export.pdf');
    }
}
