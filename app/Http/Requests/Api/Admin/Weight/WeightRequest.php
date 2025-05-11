<?php

namespace App\Http\Requests\Api\Admin\Weight;

use Illuminate\Foundation\Http\FormRequest;

class WeightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('weight_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
        ];
    }
}
