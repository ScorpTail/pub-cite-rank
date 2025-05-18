<?php

namespace App\Http\Resources\Publication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $res = [
            'id' => $this->id,
            'title' => $this->title,
            'authors' => $this->authors()->orderBy('author_position', 'asc')->get()->map(function ($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->full_name,
                ];
            }),
            'categories' => $this->categories()->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
            'published_at' => $this->published_at,
            'publisher_id' => $this->publisher_id,

            'citation_count' => $this->citation_count,
            'doi' => $this->doi,
            'openalex_id' => 'https://openalex.org/concepts/C' . $this->openalex_id,
        ];

        if ($this->publisher) {
            $code = strtolower($this->publisher->country);

            $res['publisher_name'] =  $this->publisher->name;
            $res['publisher_country'] = "https://flagcdn.com/48x36/{$code}.png";
        }

        return $res;
    }
}
