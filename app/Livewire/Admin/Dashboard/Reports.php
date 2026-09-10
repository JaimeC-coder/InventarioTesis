<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

class Reports extends Component
{
    public array $reports = [];

    public string $startDate;

    public string $endDate;

    public array $listreports = [];

    public function updatedStartDate(string $value): void
    {
        $this->startDate = $value;
    }

    public function updatedEndDate(string $value): void
    {
        $this->endDate = $value;
    }

    public function mount(): void
    {
        // Inicializa la propiedad $reports con un arreglo vacío
        $this->reports = [
            ['id' => 1, 'name' => 'Reporte de Ventas'],
            ['id' => 2, 'name' => 'Reporte de compras'],
            ['id' => 3, 'name' => 'Reporte de inventario general'],
            ['id' => 4, 'name' => 'Reporte de inventario por producto'],
            ['id' => 5, 'name' => 'Reporte de inventario por almacén'],
            ['id' => 6, 'name' => 'Reporte de inventario por categoría'],
            ['id' => 7, 'name' => 'Reporte de inventario por proveedor'],
            ['id' => 8, 'name' => 'Reporte de inventario por cliente'],
            ['id' => 9, 'name' => 'Reporte de inventario por fecha'],
        ];
        $this->listreports = [
            ['id' => 1, 'name' => 'Reporte de Ventas', 'download_link' => '/reports/sales/reporte_ventas.pdf', 'description' => '27/07/2023 - 29/07/2023'],
            ['id' => 2, 'name' => 'Reporte de compras', 'download_link' => '/reports/purchases/reporte_compras.pdf', 'description' => '27/07/2023 - 29/07/2023'],
            ['id' => 3, 'name' => 'Reporte de inventario general', 'download_link' => '/reports/inventory/reporte_inventario_general.pdf', 'description' => '27/07/2023 - 29/07/2023'],
            ['id' => 4, 'name' => 'Reporte de inventario por producto', 'download_link' => '/reports/inventory/reporte_inventario_producto.pdf', 'description' => '27/07/2023 - 29/07/2023'],
            ['id' => 5, 'name' => 'Reporte de inventario por almacén', 'download_link' => '/reports/inventory/reporte_inventario_almacen.pdf', 'description' => '27/07/2023 - 29/07/2023'],
        ];
    }

    public function generateReport(): void
    {
        // Aquí puedes agregar la lógica para generar el reporte
        // Por ejemplo, podrías llamar a un servicio o realizar consultas a la base de datos
        // y luego almacenar los resultados en la propiedad $reports.
        // Ejemplo de generación de reporte ficticio:
        $this->reports = [
            ['id' => 1, 'name' => 'Reporte de Ventas', 'date' => now()->toDateString()],
            ['id' => 2, 'name' => 'Reporte de Compras', 'date' => now()->toDateString()],
            ['id' => 3, 'name' => 'Reporte de Inventario', 'date' => now()->toDateString()],
        ];
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.reports');
    }
}
