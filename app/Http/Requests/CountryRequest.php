<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:countries,name,' . ($this->route('country')->id ?? 'NULL') . ',id',
            'code_iso' => 'required|string|min:2|max:2|unique:countries,code_iso,' . ($this->route('country')->id ?? 'NULL') . ',id',
            'emoji' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ];
    }
}
