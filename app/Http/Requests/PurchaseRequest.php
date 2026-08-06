<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class PurchaseRequest extends FormRequest
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

    protected function rulesPost(): array
    {
        return [
            'voucher_type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'total' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'voucher_type.required' => 'El tipo de comprobante es obligatorio.',
            'voucher_type.in' => 'El tipo de comprobante debe ser 1 (Factura) o 2 (Boleta).',
            'serie.required' => 'La serie es obligatoria.',
            'serie.string' => 'La serie debe ser una cadena de texto.',
            'serie.max' => 'La serie no puede tener más de 20 caracteres.',
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.integer' => 'El correlativo debe ser un número entero.',
            'correlativo.min' => 'El correlativo debe ser al menos 1.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha no tiene un formato válido.',
            'supplier_id.required' => 'El proveedor es obligatorio.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no existe.',
            'purchase_order_id.exists' => 'La orden de compra seleccionada no existe.',
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser al menos 0.01.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
            'observation.max' => 'La observación no puede tener más de 500 caracteres.',
            'products.required' => 'Debe agregar al menos un producto a la compra.',
            'products.array' => 'Los productos deben enviarse como un arreglo.',
            'products.min' => 'Debe agregar al menos un producto a la compra.',
            'products.*.id.required' => 'El ID del producto es obligatorio.',
            'products.*.id.exists' => 'El producto seleccionado no existe.',
            'products.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'products.*.quantity.integer' => 'La cantidad del producto debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad del producto debe ser al menos 1.',
            'products.*.price.required' => 'El precio del producto es obligatorio.',
            'products.*.price.numeric' => 'El precio del producto debe ser un número.',
            'products.*.price.min' => 'El precio del producto debe ser al menos 0.',
        ];
    }

    public function attributes(): array
    {
        return [
            'voucher_type' => 'Tipo de comprobante',
            'warehouse_id' => 'ID del almacén',
            'purchase_order_id' => 'ID de la orden de compra',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'supplier_id' => 'Proveedor',
            'total' => 'Total',
            'observation' => 'Observación',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ];
    }
}
