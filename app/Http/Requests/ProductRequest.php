<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Reemplazamos uuid por id de forma segura
        if (isset($validated['category_uuid'])) {
            $validated['category_id'] = \App\Models\Category::where('uuid', $validated['category_uuid'])->value('id');
            unset($validated['category_uuid']); // 🔹 ya no viaja al controlador
        }

        return $validated;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function typeMetodo(): ?string
    {
        switch ($this->method()) {
            case 'POST':
                $this->rulesPost();
                break;
            case 'PUT':
                $this->rulesPut();
                break;
            case 'DELETE':
                $this->rulesDestroy();
                break;
            case 'PATCH':
                $this->rulesPatch();
                break;
            default:
                return 'index';
        }
        return null;
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => $this->rulesPost(),
            'PUT' => $this->rulesPut(),
            //'DELETE' => $this->rulesDestroy(),
            'PATCH' => $this->rulesPatch(),
            default => [],
        };
    }

    protected function sharedRules(): array
    {
        return [
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'category_uuid' => 'required|exists:categories,uuid',
        ];
    }

    protected function rulesPost(): array
    {
        return array_merge($this->sharedRules(), [
            'name' => 'required|string|max:255|unique:products,name',
        ]);
    }

    protected function rulesPut(): array
    {
        return array_merge($this->sharedRules(), [
            'name' => 'required|string|max:255|unique:products,name,' . $this->product->id,
        ]);
    }

    protected function rulesDestroy(): array
    {
        return [
            'uuid' => 'required|exists:categories,uuid',
        ];
    }

    protected function rulesPatch(): array
    {
        return [
            'uuid' => 'required|exists:categories,uuid',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_uuid' => 'required|exists:categories,uuid',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es requerido',
            'name.string' => 'El nombre del producto debe ser una cadena de texto',
            'name.max' => 'El nombre del producto no debe exceder los 255 caracteres',
            'description.required' => 'La descripción del producto es requerida',
            'description.string' => 'La descripción del producto debe ser una cadena de texto',
            'uuid.required' => 'El identificador del producto es requerido',
            'uuid.exists' => 'El identificador del producto no existe',
            'price.required' => 'El precio del producto es requerido',
            'price.numeric' => 'El precio del producto debe ser un número',
            'price.min' => 'El precio del producto debe ser al menos 0',
            'category_uuid.required' => 'La categoría del producto es requerida',
            'category_uuid.exists' => 'La categoría del producto no existe',
        ];
    }
}
