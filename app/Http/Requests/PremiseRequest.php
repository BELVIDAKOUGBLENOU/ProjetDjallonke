<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PremiseRequest extends FormRequest
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
            'village_id' => 'required|exists:villages,id',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('premises', 'code')->ignore($this->premise),
            ],
            'address' => 'nullable|string|max:255',
            'gps_coordinates' => 'nullable|string|max:255',
            'type' => 'required|string|max:255|in:FARM,MARKET,SLAUGHTERHOUSE,PASTURE,TRANSPORT',
            'health_status' => 'nullable|string|max:255',
        ];
    }
}
