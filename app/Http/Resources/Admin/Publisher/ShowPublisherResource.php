<?php

namespace App\Http\Resources\Admin\Publisher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowPublisherResource extends JsonResource
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
            'name' => $this->name,
            'country' => $this->country,
            'website' => $this->website,
            'h_index' => $this->h_index,
            'openalex_id' => $this->openalex_id,
            'top_publications' => $this->publications()->orderBy('citation_count', 'desc')->limit(5)->get(),
            'created_at' => $this->created_at,
        ];
    }
}
