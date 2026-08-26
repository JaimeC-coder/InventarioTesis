<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_min' => 'nullable|integer|min:0',
            'category_code' => 'required',
            'productBaseName' => 'required|string|max:255',
            'units_uuid' => 'required|array|min:1',
            'measures_uuid' => 'required|array|min:1',
            'products' => 'required|array|min:1',
            'products.*.codigo' => 'required|string|max:255',
            'products.*.name' => 'required|string|max:255',
            'products.*.unituuid' => 'required|exists:units,uuid',
            'products.*.measureuuid' => 'required|exists:measures,uuid',
            'products.*.price_sale' => 'required|numeric|min:0',
            'products.*.price_purchase' => 'required|numeric|min:0',
            'category_uuid' => 'required|exists:categories,uuid',
            'supplier_uuid' => 'required|exists:suppliers,uuid',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
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
            'code.required' => 'El código del producto es requerido',
            'code.string' => 'El código del producto debe ser una cadena de texto',
            'code.max' => 'El código del producto no debe exceder los 255 caracteres',
            'description.required' => 'La descripción del producto es requerida',
            'description.string' => 'La descripción del producto debe ser una cadena de texto',
            'stock_min.integer' => 'El stock mínimo del producto debe ser un número entero',
            'stock_min.min' => 'El stock mínimo del producto debe ser al menos 0',
            'category_code.required' => 'El código de la categoría es requerido',
            'productBaseName.required' => 'El nombre base del producto es requerido',
            'productBaseName.string' => 'El nombre base del producto debe ser una cadena de texto',
            'productBaseName.max' => 'El nombre base del producto no debe exceder los 255 caracteres',
            'units_uuid.required' => 'Debe seleccionar al menos una unidad',
            'units_uuid.array' => 'Las unidades deben ser un arreglo',
            'units_uuid.min' => 'Debe seleccionar al menos una unidad',
            'measures_uuid.required' => 'Debe seleccionar al menos una medida',
            'measures_uuid.array' => 'Las medidas deben ser un arreglo',
            'measures_uuid.min' => 'Debe seleccionar al menos una medida',
            'products.required' => 'Debe agregar al menos un producto',
            'products.array' => 'Los productos deben ser un arreglo',
            'products.min' => 'Debe agregar al menos un producto',
            'products.*.codigo.required' => 'El código del producto es requerido',
            'products.*.codigo.string' => 'El código del producto debe ser una cadena de texto',
            'products.*.codigo.max' => 'El código del producto no debe exceder los 255 caracteres',
            'products.*.name.required' => 'El nombre del producto es requerido',
            'products.*.name.string' => 'El nombre del producto debe ser una cadena de texto',
            'products.*.name.max' => 'El nombre del producto no debe exceder los 255 caracteres',
            'products.*.unituuid.required' => 'La unidad del producto es requerida',
            'products.*.unituuid.exists' => 'La unidad del producto no existe',
            'products.*.measureuuid.required' => 'La medida del producto es requerida',
            'products.*.measureuuid.exists' => 'La medida del producto no existe',
            'products.*.price_sale.required' => 'El precio de venta del producto es requerido',
            'products.*.price_sale.numeric' => 'El precio de venta del producto debe ser un número',
            'products.*.price_sale.min' => 'El precio de venta del producto debe ser al menos 0',
            'products.*.price_purchase.required' => 'El precio de compra del producto es requerido',
            'products.*.price_purchase.numeric' => 'El precio de compra del producto debe ser un número',
            'products.*.price_purchase.min' => 'El precio de compra del producto debe ser al menos 0',
            'products.*.stock.required' => 'El stock del producto es requerido',
            'products.*.stock.integer' => 'El stock del producto debe ser un número entero',
            'products.*.stock.min' => 'El stock del producto debe ser al menos 0',
            'products.*.alert_stock.integer' => 'El stock de alerta del producto debe ser un número entero',
            'products.*.alert_stock.min' => 'El stock de alerta del producto debe ser al menos 0',
            'category_uuid.required' => 'La categoría del producto es requerida',
            'category_uuid.exists' => 'La categoría del producto no existe',
            'supplier_uuid.required' => 'El proveedor del producto es requerido',
            'supplier_uuid.exists' => 'El proveedor del producto no existe',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'código del producto',
            'description' => 'descripción del producto',
            'stock_min' => 'stock mínimo del producto',
            'category_code' => 'código de la categoría',
            'productBaseName' => 'nombre base del producto',
            'products' => 'productos',
            'products.*.codigo' => 'código del producto',
            'products.*.name' => 'nombre del producto',
            'products.*.unituuid' => 'unidad del producto',
            'products.*.measureuuid' => 'medida del producto',
            'products.*.price_sale' => 'precio de venta del producto',
            'products.*.price_purchase' => 'precio de compra del producto',
            'products.*.stock' => 'stock del producto',
            'products.*.alert_stock' => 'stock de alerta del producto',
            'category_uuid' => 'categoría del producto',
            'supplier_uuid' => 'proveedor del producto',
        ];
    }
}
