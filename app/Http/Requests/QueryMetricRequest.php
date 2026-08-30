<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QueryMetricRequest extends FormRequest
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
            'entity'   => ['required', Rule::in(['customer', 'product', 'sale', 'conversion'])],
            'metric'   => ['required', 'string'],
            'filters'                => ['sometimes', 'array'],
            'filters.year'           => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'filters.date_from'      => ['sometimes', 'date'],
            'filters.date_to'        => ['sometimes', 'date', 'after_or_equal:filters.date_from'],
            'filters.customer_ref'   => ['sometimes', 'string', 'max:100'], // uuid del customer
            'sort_direction'         => ['sometimes', Rule::in(['asc', 'desc'])],
            'limit'                  => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }
}
