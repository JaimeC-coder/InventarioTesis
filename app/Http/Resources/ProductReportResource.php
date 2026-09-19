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
            // 'reference' => $this->uuid,
            'name' => $this->when(isset($this->name), fn(): string => (string) $this->name),
            'total_sold' => $this->when(isset($this->total_sold), fn(): int => (int) $this->total_sold),
            'total_purchased' => $this->when(isset($this->total_purchased), fn(): int => (int) $this->total_purchased),
            'stock_level' => $this->when(isset($this->stock_level), fn(): int => (int) $this->stock_level),
            'warehouse_name' => $this->when(isset($this->warehouse_name), fn(): string => (string) $this->warehouse_name),
        ];
    }
}
