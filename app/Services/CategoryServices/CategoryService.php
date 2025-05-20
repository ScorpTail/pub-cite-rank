<?php

namespace App\Services\CategoryServices;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function category(array $param = [], ?int $categoryId = null)
    {
        return $categoryId
            ? $this->getCategory($categoryId, $param)
            : $this->getCategories($param);
    }

    private function getCategories(array $param = [])
    {
        $query = Category::query();

        if (isset($param['name'])) {
            $query->where('name', 'like', '%' . $param['name'] . '%');
        }

        return $query->paginate(15);
    }

    private function getCategory(int $categoryId, array $param = [])
    {
        $query = Category::query();

        $query->where('id', $categoryId);

        return $query->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Category::create($data);
        });
    }

    public function update(string $categoryId, array $data)
    {
        return DB::transaction(function () use ($categoryId, $data) {
            return Category::where('id', $categoryId)->update($data);
        });
    }

    public function delete(string $categoryId)
    {
        return DB::transaction(function () use ($categoryId) {
            return Category::where('id', $categoryId)->delete();
        });
    }
}
