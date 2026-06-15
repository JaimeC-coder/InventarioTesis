<?php

namespace App\Livewire\Admin\Dashboard;

use App\Enum\MountEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GraficaPrincipal extends Component
{
    public $ventasPorMes = [];

    public $mesSeleccionado;

    public $arrayMeses = [];

    public function mount(): void
    {
        $this->arrayMeses = collect(MountEnum::cases())->map(fn($mes): array => [
            'id' => $mes->value,
            'name' => $mes->label(),
        ])->toArray();
        $this->mesSeleccionado = Carbon::now()->month;
        $this->getVentasPorMesFormattedProperty();
    }

    public function getVentasPorMesFormattedProperty(): void
    {
        /**
         *   $entradasSalidasXMes = DB::table('inventory_transactions')
                ->select('transactionType', DB::raw('SUM(transactionCount) as cantidad'), DB::raw('MONTH(transactionDate) as mes'))
                ->whereRaw('YEAR(transactionDate) = ' . $year)
                ->groupBy('transactionType', 'mes')
                ->get();
         */
        $ventasPorMes = \App\Models\Sale::selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $comprasPorMes = \App\Models\Purchase::selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $moviminetosPorMes = \App\Models\Movement::selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $labels = [
            'Ventas',
            'Compras',
            'Movimientos',
        ];
        $data = [
            $ventasPorMes->pluck('total'),
            $comprasPorMes->pluck('total'),
            $moviminetosPorMes->pluck('total'),
        ];
        Log::info('Ventas por mes: ' . $ventasPorMes);
        Log::info('Compras por mes: ' . $comprasPorMes);
        Log::info('Movimientos por mes: ' . $moviminetosPorMes);
        $this->dispatch(
            'updateChart',
            labels: $labels,
            data: $data,
            text: 'Entradas , Salidas y Movimientos de ' . $this->arrayMeses[$this->mesSeleccionado - 1]['name']
        );
    }

    public function ventasEspecificas($mes): void
    {
        $ventasPorMes = \App\Models\Sale::whereMonth('date', $mes)->selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $comprasPorMes = \App\Models\Purchase::whereMonth('date', $mes)->selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $moviminetosPorMes = \App\Models\Movement::whereMonth('date', $mes)->selectRaw('MONTH(date) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $labels = [
            'Ventas',
            'Compras',
            'Movimientos',
        ];
        $data = [
            $ventasPorMes->pluck('total'),
            $comprasPorMes->pluck('total'),
            $moviminetosPorMes->pluck('total'),
        ];
        $this->dispatch(
            'updateChart',
            labels: $labels,
            data: $data,
            text: 'Entradas , Salidas y Movimientos de ' . $this->arrayMeses[$this->mesSeleccionado - 1]['name']
        );
    }

    public function updatedMesSeleccionado($value): void
    {
        $ventasPorMes = \App\Models\Sale::leftJoin('warehouses', 'sales.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('MONTH(sales.date) as mes, warehouses.name as warehouse_name, COUNT(sales.id) as total')
            ->groupByRaw('MONTH(sales.date), warehouses.name')
            ->orderBy('mes')
            ->get();
        Log::info('Ventas por mes: ' . $ventasPorMes);
        $this->ventasEspecificas($value);
    }

    /**
     * public function inputOuputXMes(Request $request)
    {
        log($request);
        $year = $request->anio ? $request->anio : date('Y');
        $productId = $request->product ? $request->product : 0;
        if ($productId === 0) {
            $entradasSalidasXMes = DB::table('inventory_transactions')
                ->select('transactionType', DB::raw('SUM(transactionCount) as cantidad'), DB::raw('MONTH(transactionDate) as mes'))
                ->whereRaw('YEAR(transactionDate) = ' . $year)
                ->groupBy('transactionType', 'mes')
                ->get();
            return JsonResponse::success($entradasSalidasXMes, 'Entradas y salidas de productos por mes', true, 1, 200);
        }
     */
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.admin.dashboard.grafica-principal');
    }
}
