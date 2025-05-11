<?php

namespace App\Http\Requests\Api\Admin\Journal;

use App\Enums\Journal\JournalTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('journal_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:' . JournalTypeEnum::getColumnLikeString('value')],
            'issn' => ['sometimes'],
            'impact_factor' => ['sometimes', 'numeric'],
        ];
    }
}
