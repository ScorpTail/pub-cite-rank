<?php

namespace App\Services\AuthorServices;

use App\Models\Author;

class AuthorService
{
    public function author(?int $authorId = null)
    {
        return $authorId
            ? $this->getAuthor($authorId)
            : $this->getAuthors();
    }

    public function getAuthors()
    {
        return Author::all();
    }

    public function getAuthor(int $authorId)
    {
        return Author::findOrFail($authorId);
    }
}
