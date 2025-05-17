<?php

namespace App\Http\Resources\Admin\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $categories = Category::get(['id', 'name'])->pluck('name', 'id');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'openalex_concept_id' => $this->openalex_concept_id,
            'level' => $this->level,
            'publications_count' => $this->whenLoaded('publications', function () {
                return $this->publications->count();
            }),
            'categories' => $categories,
            'created_at' => $this->created_at,

            'hierarchy' => $this->getHierarchy()
        ];
    }
}
