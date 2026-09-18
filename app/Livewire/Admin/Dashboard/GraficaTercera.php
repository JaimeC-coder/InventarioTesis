<?php

namespace App\Livewire\Admin\Dashboard;

use App\Enum\MountEnum;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GraficaTercera extends Component
{
    // - Grafica de los productos mas vendido por almacen en el mes
    public int $mount = 0;

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
        $this->saleProductXWarehoseMount($this->mesSeleccionado);
    }

    public function updatedMesSeleccionado(int|null $value): void
    {
        Log::info('Mes seleccionado es nulo, se asigna el mes actual.......');
        if (is_null($value)) {
            Log::info('Mes seleccionado es nulo, se asigna el mes actual');
            $value = Carbon::now()->month;
            $this->saleProductXWarehoseMount($value);
        }

        $this->saleProductXWarehoseMount($value);
    }

    public function saleProductXWarehoseMount(int $mount = 0): void
    {
        $sales = Sale::select(DB::raw('SUM(sales.total) as total'), 'sales.warehouse_id', 'warehouses.name')
            ->join('warehouses', 'sales.warehouse_id', '=', 'warehouses.id')
            ->whereMonth('date', $mount)
            ->groupBy('warehouse_id')
            ->get();
        $this->dispatch(
            'updateChart3',
            labels: $sales->pluck('name')->toArray(),
            data: $sales->pluck('total')->toArray(),
            text: 'Montos de las ventas por almacen en el mes de  ' . $this->nombreMes($mount) . ' de ' . Carbon::now()->year
        );
    }

    private function nombreMes(int $mes): string
    {
        return collect($this->arrayMeses)
            ->firstWhere('id', $mes)['name'] ?? (string) $mes;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.grafica-tercera');
    }
}
