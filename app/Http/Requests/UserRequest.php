<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . ($this->user?->id ?? 'NULL')],
            'phone' => ['nullable', 'string', 'max:30'],
            'person_id' => ['nullable', 'exists:persons,id'],
            // 'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:6'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'person_id.exists' => 'La personne sélectionnée n’existe pas.',
            'roles.*.exists' => 'Un des rôles sélectionnés est invalide.',
            'permissions.*.exists' => 'Une des permissions sélectionnées est invalide.',
        ];
    }
}
