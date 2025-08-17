<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'nullable',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user), // kalau route model binding
            ],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name'
        ];
    }

    function messages()
    {
        return [
            'email' => 'The :attribute field must be a valid email address.',
            'unique' => 'The :attribute field must be unique.',
            'array' => 'The :attribute field must be an array.',
            'exists' => 'The :attribute field must exist.',
        ];
    }
}
