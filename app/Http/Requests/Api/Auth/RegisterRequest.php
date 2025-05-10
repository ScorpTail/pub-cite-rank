<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'middle_name' => ['sometimes'],
            'last_name' => ['sometimes'],
            'birth_date' => ['sometimes', 'date'],
            'email' => ['required', 'string', 'email:rfc,dns', 'ends_with:.com,.net,.ua', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ];
    }
}
