<?php

namespace App\Http\Requests\Api\Admin\User;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->can('user_create', 'api') || auth()->user()->can('user_edit', 'api'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'date'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $this->route('userId')],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'string', 'exists:permissions,id'],
            'status' => ['sometimes', 'string', 'in:' . StatusEnum::getColumnLikeString('value')],
        ];
    }
}
