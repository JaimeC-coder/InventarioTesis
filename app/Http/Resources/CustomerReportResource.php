<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerReportResource extends JsonResource
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
            'total_revenue' => $this->when(isset($this->total_revenue), fn(): float => (float) $this->total_revenue),
            'purchase_count' => $this->when(isset($this->purchase_count), fn(): int => (int) $this->purchase_count),
            // nunca: id, email, phone, document_number
        ];
    }
}
