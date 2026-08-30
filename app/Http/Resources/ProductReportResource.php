<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'reference' => $this->uuid,
            'name' => $this->name,
            'total_sold' => isset($this->total_sold) ? (int) $this->total_sold : null,
            'total_purchased' => isset($this->total_purchased) ? (int) $this->total_purchased : null,
            'stock_level' => isset($this->stock_level) ? (int) $this->stock_level : null,
            'warehouse_name' => $this->warehouse_name ?? null,
        ];
    }
}
