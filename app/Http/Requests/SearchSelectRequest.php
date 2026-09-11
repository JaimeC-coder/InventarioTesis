<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SearchSelectRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:100'],
            'selected' => ['nullable', 'array', 'max:50'],
            'selected.*' => [
                'required',
                function ($attribute, $value, $fail): void {
                    if (!Str::isUuid($value) && !(is_numeric($value) && (int) $value == $value && $value > 0)) {
                        $fail(sprintf('El valor en %s debe ser un UUID válido o un ID numérico entero.', $attribute));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'selected.*.uuid' => 'Uno de los elementos seleccionados no es válido.',
            'selected.max' => 'No se pueden seleccionar más de 50 elementos a la vez.',
        ];
    }
}
