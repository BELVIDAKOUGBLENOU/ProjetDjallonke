<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WeightRecordRequest extends FormRequest
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
                Rule::unique('weight_records', 'uid')->ignore($this->weight_record),
            ],
            'event_id' => 'required|exists:events,id',
            'weight' => 'required|numeric',
            'age_days' => 'nullable|integer',
            'measure_method' => 'nullable|string|max:255',
        ];
    }
}
