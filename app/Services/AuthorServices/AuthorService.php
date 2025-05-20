<?php

namespace App\Services\AuthorServices;

use App\Models\Author;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    public function author(array $param = [], ?int $authorId = null)
    {
        return $authorId
            ? $this->getAuthor($authorId)
            : $this->getAuthors($param);
    }

    public function getAuthors(array $param = [])
    {
        $query = Author::query();

        if (isset($param['name'])) {
            $query->where('first_name', 'like', '%' . $param['name'] . '%')
                ->orWhere('middle_name', 'like', '%' . $param['name'] . '%')
                ->orWhere('last_name', 'like', '%' . $param['name'] . '%');
        }

        return $query->paginate(15);
    }

    public function getAuthor(int $authorId, array $param = [])
    {
        $query = Author::query();

        $query->where('id', $authorId);

        return $query->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Author::create($data);
        });
    }

    public function update(string $authorId, array $data)
    {
        return DB::transaction(function () use ($authorId, $data) {
            return Author::where('id', $authorId)->update($data);
        });
    }

    public function delete(string $authorId)
    {
        return DB::transaction(function () use ($authorId) {
            return Author::where('id', $authorId)->delete();
        });
    }
}
