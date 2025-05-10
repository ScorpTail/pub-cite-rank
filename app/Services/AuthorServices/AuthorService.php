<?php

namespace App\Services\AuthorServices;

use App\Models\Author;

class AuthorService
{
    public function author(?int $authorId = null, array $param = [])
    {
        return $authorId
            ? $this->getAuthor($authorId)
            : $this->getAuthors();
    }

    public function getAuthors(array $param = [])
    {
        $query = Author::query();

        return $query->get();
    }

    public function getAuthor(int $authorId, array $param = [])
    {
        $query = Author::query();

        $query->where('id', $authorId);

        return $query->first();
    }
}
