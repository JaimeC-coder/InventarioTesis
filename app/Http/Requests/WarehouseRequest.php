<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre para el almacén es obligatorio.',
            'name.string' => 'El nombre del almacén debe ser una cadena de texto.',
            'name.max' => 'El nombre del almacén no debe exceder los 255 caracteres.',
            'location.required' => 'La dirección del almacén es obligatoria.',
            'location.string' => 'La dirección del almacén debe ser una cadena de texto.',
            'location.max' => 'La dirección del almacén no debe exceder los 255 caracteres.',
        ];
    }
}
