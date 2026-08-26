<?php

namespace App\Http\Requests;

use App\Enum\DocumentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;

class CustomerRequest extends FormRequest
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
        if (isset($validated['identity_uuid'])) {
            $validated['identity_id'] = \App\Models\Identity::where('uuid', $validated['identity_uuid'])->value('id');
            unset($validated['identity_uuid']); // 🔹 ya no viaja al controlador
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
        Log::alert('Obteniendo reglas para método: ' . $this->method());
        return match ($this->method()) {
            'POST' => $this->rulesPost(),
            'PUT' => $this->rulesPut(),
            //'DELETE' => $this->rulesDestroy(),
            'PATCH' => $this->rulesPatch(),
            default => [],
        };
    }

    public function rulesForAction(string $action): array
    {
        return match (strtoupper($action)) {
            'POST'   => $this->rulesPost(),
            'PUT'    => $this->rulesPut(),
            'PATCH'  => $this->rulesPatch(),
            'DELETE' => $this->rulesDestroy(),
            'SHOW'   => $this->rulesShow(),
            default  => $this->rulesGet(),
        };
    }

    protected function sharedRules(): array
    {
        return [
            'identity' => [
                'required',
                new Enum(DocumentEnum::class),
            ],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9\-\(\)\s]+$/|min:7',
            'email' => 'required|email|max:255',
            'type' => 'required|in:GENERAL,A1',
        ];
    }

    protected function rulesPost(): array
    {
        return array_merge($this->sharedRules(), [
            'document_number' => 'required|numeric|unique:customers,document_number|min_digits:8',
        ]);
    }

    protected function rulesPut(): array
    {
        return array_merge($this->sharedRules(), [
            'document_number' => 'required|numeric|unique:customers,document_number,' . $this->customer->id . '|min_digits:8',
        ]);
    }

    protected function rulesDestroy(): array
    {
        return [
            'uuid' => 'required|exists:customers,uuid',
        ];
    }

    protected function rulesPatch(): array
    {
        return [
            'document_number' => 'required|numeric|unique:customers,document_number,' . $this->customer->id . '|min_digits:8',
            'identity' => [
                'required',
                new Enum(DocumentEnum::class),
            ],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9\-\(\)\s]+$/|min:7',
            'email' => 'required|email|max:255',
            'type' => 'required|in:GENERAL,A1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es requerido',
            'name.string' => 'El nombre del cliente debe ser una cadena de texto',
            'name.max' => 'El nombre del cliente no debe exceder los 255 caracteres',
            'address.required' => 'La dirección del cliente es requerida',
            'address.string' => 'La dirección del cliente debe ser una cadena de texto',
            'address.max' => 'La dirección del cliente no debe exceder los 255 caracteres',
            'phone.required' => 'El teléfono del cliente es requerido',
            'phone.string' => 'El teléfono del cliente debe ser una cadena de texto',
            'phone.max' => 'El teléfono del cliente no debe exceder los 20 caracteres',
            'phone.regex' => 'El teléfono del cliente debe ser un número válido',
            'phone.min' => 'El teléfono del cliente debe tener al menos 7 caracteres',
            'email.required' => 'El correo electrónico del cliente es requerido',
            'email.email' => 'El correo electrónico del cliente debe ser una dirección de correo electrónico válida',
            'email.max' => 'El correo electrónico del cliente no debe exceder los 255 caracteres',
            'document_number.required' => 'El número de documento es requerido',
            'document_number.numeric' => 'El número de documento debe ser un número',
            'document_number.unique' => 'El número de documento ya está en uso',
            'identity.required' => 'El identificador de la identidad es requerido',
            'identity.enum' => 'El identificador de la identidad no es válido',
        ];
    }

    // public function prepareForValidation()
    // {
    //     dd($this->all());
    // }
}
