<?php

namespace App\Http\Requests\Api\Admin\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->can('publisher_create', 'api') || auth()->user()->can('publisher_edit', 'api'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'country' => ['sometimes'],
            'website' => ['sometimes', 'url'],
            'h_index' => ['sometimes'],
            'openalex_id' => ['sometimes', 'unique:publishers,openalex_id,' . $this->route('publisherId') . ',id'],
        ];
    }
}
