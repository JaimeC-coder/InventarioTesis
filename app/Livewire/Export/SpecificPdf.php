<?php

namespace App\Livewire\Export;

use Barryvdh\DomPDF\Facade\Pdf as domPdf;
use Carbon\Carbon;
use Livewire\Component;

class SpecificPdf extends Component
{
    // openPdfExport-specific
    #[\Livewire\Attributes\On('openPdfExport-specific')]
    public function generatePdfNow(array $payload)
    {
        $model = $payload['model'];
        $uuid = $payload['uuids'];
        // Buscar datos
        $items = $model::with([
            'products' => function ($q): void {
                $q->orderBy('productables.id')->limit(100);
            },
        ])->where('uuid', $uuid)
            ->firstOrFail();
        $name = 'Reporte_' . $this->replacename($model) .  '_' . $this->replacename($items->supplier->name)  . '_' . Carbon::parse($items->date)->format('Y-m-d') . '.pdf';
        // quiero ver todos los items de productos relacionados al modelo
        $pdf = domPdf::loadView('export.specific-pdf', [
            'items' => $items,
            'data' => $items,
            'products' => $items->products,
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->stream();
        }, $name);
    }

    protected function replacename($name): string|array
    {
        // quitar a name App\Models\
        $name = str_replace('App\Models\\', '', $name);
        // remplazar espacios por guion bajo
        $name = str_replace(' ', '_', $name);
        return $name;
    }

    public function totalEnLetras($monto, $moneda = 'SOLES'): string
    {
        $numberFormatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($monto);
        $decimales = str_pad(round(($monto - $entero) * 100), 2, '0', STR_PAD_LEFT);

        return mb_strtoupper(
            $numberFormatter->format($entero) . sprintf(' %s CON %s/100', $moneda, $decimales)
        );
    }

    /**
     * 🥈 OPCIÓN LATAM (moneda incluida)
        Uso:

        echo totalEnLetras(1589.40, 'SOLES');

        MIL QUINIENTOS OCHENTA Y NUEVE SOLES CON 40/100
     */
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.export.specific-pdf');
    }
}
