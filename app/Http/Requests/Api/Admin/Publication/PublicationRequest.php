<?php

namespace App\Http\Requests\Api\Admin\Publication;

use Illuminate\Foundation\Http\FormRequest;

class PublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->can('publish_create', 'api') || auth()->user()->can('publish_edit', 'api'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'published_at' => ['sometimes', 'date'],
            'publisher_id' => ['required', 'max:255', 'exists:publishers,id'],
            'author_id' => ['required', 'exists:authors,id'],
            'category_id' => ['required', 'max:255', 'exists:categories,id'],
            'citation_count' => ['sometimes', 'max:255'],
            'doi' => ['sometimes', 'string', 'max:255', 'unique:publications,doi,' . $this->route('publicationId') . ',id'],
            'openalex_id' => ['sometimes', 'unique:publications,openalex_id,' . $this->route('publicationId') . ',id'],
        ];
    }

    protected function prepareForValidation()
    {
        $filtered = array_filter($this->all());

        $this->replace($filtered);
    }
}
