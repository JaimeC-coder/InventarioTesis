<?php

namespace App\Http\Requests;

class SearchReasonRequest extends SearchSelectRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'type' => ['required', 'in:1,2'],
        ]);
    }
}
