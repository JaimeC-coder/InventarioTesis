<?php

namespace App\Livewire\Admin\Dashboard;

use App\Enum\MountEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GraficaPrincipal extends Component
{
    public $ventasPorMes = [];

    public int $mesSeleccionado;



    public $arrayMeses = [];

    public function mount(): void
    {
        $this->arrayMeses = collect(MountEnum::cases())->map(fn($mes): array => [
            'id' => $mes->value,
            'name' => $mes->label(),
        ])->toArray();
        $this->mesSeleccionado = Carbon::now()->month;
        $this->cargarResumenMes($this->mesSeleccionado);
    }

    public function cargarResumenMes(int $mes): void
    {
        $data = [
            'Ventas' => \App\Models\Sale::WhereMonth('date', $mes)->count(),
            'Compras' => \App\Models\Purchase::WhereMonth('date', $mes)->count(),
            'Movimientos' => \App\Models\Movement::WhereMonth('date', $mes)->count(),
        ];

        $this->dispatch(
            'updateChart',
            labels: array_keys($data),
            data: array_values($data),
            text: 'Entradas , Salidas y Movimientos de ' . $this->nombreMes($mes) . ' de ' . Carbon::now()->year
        );
    }

    public function updatedMesSeleccionado(int $value): void
    {
        $this->cargarResumenMes($value);
    }

    private function nombreMes(int $mes): string
    {
        return collect($this->arrayMeses)
            ->firstWhere('id', $mes)['name'] ?? (string) $mes;
    }


    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.admin.dashboard.grafica-principal');
    }
}
