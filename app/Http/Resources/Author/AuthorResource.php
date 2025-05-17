<?php

namespace App\Http\Resources\Author;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'full_name'    => $this->full_name,
            'birth_date'   => $this->birth_date,
            'about'        => $this->about,
            'orcid'        => $this->orcid,
            'affiliation'  => $this->affiliation,
            'openalex_id'  => $this->openalex_id,
            'rank'         => $this->rank,
            'publications' => $this->publications,
        ];
    }
}
