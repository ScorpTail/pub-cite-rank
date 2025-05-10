<?php

namespace App\Http\Requests\Api;

use App\Models\PasswordResetToken;
use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
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
        if ($this->route()->getName() == 'auth.forgot-password') {
            $rule = [
                'email' => ['required', 'string', 'email:rfc,dns', 'ends_with:.com,.net,.ua', 'exists:users,email'],
            ];
        } else {
            $rule = [
                'password' => ['required', 'string', 'confirmed', 'min:8', 'max:26'],
                'token' => ['required', 'string'],
            ];
        }

        return $rule;
    }

    protected function prepareForValidation() {}

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->route()->getName() == 'auth.reset-password') {
                $resetToken = PasswordResetToken::query()
                    ->where('token', $this->token)
                    ->first();

                if (!$resetToken) {
                    return $validator->errors()->add('token', __('front.auth.invalid_token'));
                }

                $isUserExists = $resetToken->user()->exists();

                if (!$isUserExists) {
                    return $validator->errors()->add('email', __('front.auth.user_not_found'));
                }

                $this->merge(['email' => $resetToken->email]);
            }
        });
    }
}