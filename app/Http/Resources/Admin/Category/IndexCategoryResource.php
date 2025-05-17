<?php

namespace App\Http\Resources\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'openalex_concept_id' => $this->openalex_concept_id,
            'level' => $this->level,
            'publications_count' => $this->whenLoaded('publications', function () {
                return $this->publications->count();
            }),
            'created_at' => $this->created_at,
        ];
    }
}
