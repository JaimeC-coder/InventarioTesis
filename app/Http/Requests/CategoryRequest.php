<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'description' => 'nullable|string|max:1000',
        ];
    }

    protected function rulesPost(): array
    {
        return array_merge($this->sharedRules(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);
    }

    protected function rulesPut(): array
    {
        return array_merge($this->sharedRules(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
        ]);
    }

    protected function rulesDestroy(): array
    {
        return [
            'uuId' => 'required|exists:categories,uuId',
        ];
    }

    protected function rulesPatch(): array
    {
        return [
            'uuId' => 'required|exists:categories,uuId',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es requerido',
            'name.string' => 'El nombre de la categoría debe ser una cadena de texto',
            'name.max' => 'El nombre de la categoría no debe exceder los 255 caracteres',
            'description.required' => 'La descripción de la categoría es requerida',
            'description.string' => 'La descripción de la categoría debe ser una cadena de texto',
            'uuid.required' => 'El identificador de la categoría es requerido',
            'uuid.exists' => 'El identificador de la categoría no existe',
        ];
    }
}
