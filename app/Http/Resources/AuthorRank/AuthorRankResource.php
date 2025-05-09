<?php

namespace App\Http\Resources\AuthorRank;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorRankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
