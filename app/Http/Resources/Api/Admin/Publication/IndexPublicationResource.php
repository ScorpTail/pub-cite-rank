<?php

namespace App\Http\Resources\Api\Admin\Publication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexPublicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'published_at' => $this->published_at,
            'publisher_id' => $this->publisher_id,
            'citation_count' => $this->citation_count,
            'doi' => $this->doi,
            'openalex_id' => $this->openalex_id,
            'authors' => $this->authors,
            'categories' => $this->categories,
        ];
    }
}
