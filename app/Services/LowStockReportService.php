<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Collection;

class LowStockReportService
{
    public ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function groupedByWarehouseAndSupplier(): Collection
    {
        return $this->productRepository->groupedByWarehouseAndSupplier();
    }
}
