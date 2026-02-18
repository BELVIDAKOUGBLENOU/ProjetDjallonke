<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Event;

class EventRequest extends FormRequest
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
            'event_date' => ['sometimes', 'required', 'date'],
            'comment' => ['nullable', 'string'],
            'is_confirmed' => ['sometimes', 'boolean'],
            'source' => ['sometimes', 'required', Rule::in(Event::SOURCES)],
            'version' => ['sometimes', 'integer'],
        ];
    }
}
