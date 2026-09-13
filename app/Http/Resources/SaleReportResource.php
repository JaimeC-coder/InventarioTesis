<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReportResource extends JsonResource
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
            'total' => $this->when(isset($this->total), fn(): float => (float) $this->total),
            'date' => $this->when(isset($this->date), fn() => $this->date),
        ];
    }
}
