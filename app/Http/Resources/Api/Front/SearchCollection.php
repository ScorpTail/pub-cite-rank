<?php

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $searchResults = $this->collection;

        $res = [];
        foreach ($searchResults as $author) {
               $res['searchResults'][] = $author;
        }

        $res['searchCount'] = $searchResults->count();

        return $res;
    }
}
