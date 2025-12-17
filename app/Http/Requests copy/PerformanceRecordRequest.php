<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerformanceRecordRequest extends FormRequest
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
            'uid' => [
                'required',
                'string',
                'max:255',
                Rule::unique('performance_records', 'uid')->ignore($this->performance_record),
            ],
            'created_by' => 'required|exists:users,id',
            'animal_id' => 'required|exists:animals,id',
            'recorded_date' => 'required|date',
            'context' => 'nullable|string|max:255',
        ];
    }
}
