<?php

namespace App\Http\Resources\Admin\Author;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowAuthorResource extends JsonResource
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
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'birth_date' => $this->birth_date,
            'about' => $this->about,
            'orcid' => $this->orcid,
            'affiliation' => $this->affiliation,
            'openalex_id' => $this->openalex_id,
            'created_at' => $this->created_at,
            'rank' => $this->rank,
            'publications' => $this->publications
        ];
    }
}
