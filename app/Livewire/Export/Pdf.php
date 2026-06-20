<?php

namespace App\Livewire\Export;

use Barryvdh\DomPDF\Facade\Pdf as domPdf;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Pdf extends Component
{
    //! aqui hay que corregir con el modelo y los datos correctos, esto es solo un ejemplo
    #[\Livewire\Attributes\On('openPdfExport')]
    public function generatePdfNow(array $uuids, string $model, string $titulo, array $columns, array $headers, string $fileName)
    {
        $model = $model;
        $uuids = $uuids;
        $titulo = $titulo;
        $columns = $columns;
        $headers = $headers;
        $fileName = $fileName;

        // Buscar datos

        $items = $model::whereIn('uuid', $uuids)->get();

        $pdf = domPdf::loadView('export.pdf', [
            'items' => $items,
            'titulo' => $titulo,
            'columns' => $columns,
            'headers' => $headers,
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf, $fileName): void {
            echo $pdf->stream();
        }, $fileName);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.export.pdf');
    }
}
