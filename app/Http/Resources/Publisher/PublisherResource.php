<?php

namespace App\Http\Resources\Publisher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
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
            'created_at' => $this->created_at,
            'total_publications' => $this->publications()->count(),
            'total_authors' => $this->publications()->with('authors')->get()->pluck('authors')->flatten()->unique('id')->count(),
        ];
    }
}
