<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnimalRequest extends FormRequest
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
            // 'uid' is generated on backend
            // 'created_by' is handled by backend
            'premises_id' => 'required|exists:premises,id',
            'species' => 'required|string|max:255',
            'sex' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'life_status' => 'required|string|max:255',
        ];
    }
}
