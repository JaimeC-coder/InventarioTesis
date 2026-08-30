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
            'reference' => $this->uuid,
            'name' => $this->name,
            'total_revenue' => isset($this->total_revenue) ? (float) $this->total_revenue : null,
            'purchase_count' => isset($this->purchase_count) ? (int) $this->purchase_count : null,
            // nunca: id, email, phone, document_number
        ];
    }
}
