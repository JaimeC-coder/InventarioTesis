<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Record;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GraficaCuarta extends Component
{
    // lista de productos sin stock o con stock bajo

    public function mount(): void
    {
        $this->notTopProducts();
    }

    public function notTopProducts(): void
    {
        // $products = Record::query()
        //     ->select('products.name', 'records.quantity', 'records.warehouse_name')
        //     ->join('products', 'records.product_id', '=', 'products.id')
        //     ->where('records.quantity', '<=', 'products.min_stock')
        //     // ->where('records.quantity', '<=', 99)
        //     ->groupBy('records.warehouse_id', 'records.product_id')
        //     ->orderBy('records.warehouse_id', 'asc')
        //     ->get();
        // Log::info('Productos con stock bajo o sin stock: ' . $products);
    }

    // - promedio de ventas por mes en el año  por almacen

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.grafica-cuarta');
    }
}
