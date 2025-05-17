<?php

namespace App\Http\Resources\Admin\Publisher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexPublisherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'country' => $this->country,
            'website' => $this->website,
            'h_index' => $this->h_index,
            'openalex_id' => $this->openalex_id,
            'publication_count' => $this->publications()->count(),
            'created_at' => $this->created_at,
        ];
    }
}
