<?php

namespace App\Livewire\Admin\Dashboard;

use Carbon\Carbon;
use Livewire\Component;

class GraficaSegunda extends Component
{
    public $ventasPorMes = [];

    public $comprasPorMes = [];

    public $movimientosPorMes = [];

    public $anioSeleccionado;

    public $arrayanio = [];

    public function mount(): void
    {
        $this->arrayanio = collect(range(2020, date('Y')))->map(fn($year): array => [
            'id' => $year,
            'name' => $year,
        ])->toArray();
        $this->anioSeleccionado = Carbon::now()->year;
        $this->cargarGrafica($this->anioSeleccionado);
    }

    public function updatedAnioSeleccionado($value): void
    {
        $this->cargarGrafica($value);
    }

    private function cargarGrafica(string $year): void
    {
        $ventasPorMes = \App\Models\Sale::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->whereYear('date', $year)
            ->groupByRaw('MONTH(date)')
            ->get()
            ->keyBy('month');
        $comprasPorMes = \App\Models\Purchase::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->whereYear('date', $year)
            ->groupByRaw('MONTH(date)')
            ->get()
            ->keyBy('month');
        $labels = collect(range(1, 12))
            ->map(fn($month): string => Carbon::create($year, $month, 1)->translatedFormat('F'))
            ->toArray();
        $ventas = collect(range(1, 12))
            ->map(fn($month): float => (float) ($ventasPorMes->get($month)->total ?? 0))
            ->values();
        $compras = collect(range(1, 12))
            ->map(fn($month): float => (float) ($comprasPorMes->get($month)->total ?? 0))
            ->values();
        // $diferencia = $ventas->map(fn($venta, $i) => $venta - $compras[$i])->values();
        $this->dispatch(
            'updateChart2',
            labels: $labels,
            data: [
                'ventas'     => $ventas,
                'compras'    => $compras,
            ],
            text: 'Ventas, Compras del año ' . $year
            // text: 'Ventas, Compras y Diferencia del año ' . $year
        );
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.grafica-segunda');
    }
}
