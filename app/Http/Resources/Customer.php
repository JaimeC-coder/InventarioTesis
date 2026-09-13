<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' =>$this->when(isset($this->uuid), fn () => (string) $this->uuid),
            'name' => $this->when(isset($this->name), fn(): string => (string) $this->name),
            'phone' => $this->when(isset($this->phone), fn(): string => (string) $this->phone),
            'document_number' => $this->when(isset($this->document_number), fn(): string => (string) $this->document_number),
            'type' => $this->when(isset($this->type), fn(): string => (string) $this->type),
            'identity' => $this->when(isset($this->identity), fn() => $this->identity?->name),
        ];
    }
}
