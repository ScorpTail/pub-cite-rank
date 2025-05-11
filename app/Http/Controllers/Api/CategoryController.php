<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CategoryServices\CategoryService;
use App\Http\Resources\Category\ShowCategoryResource;
use App\Http\Resources\Category\IndexCategoryResource;

class CategoryController extends Controller
{
    public function __construct(
        public CategoryService $categoryService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return IndexCategoryResource::collection(
            $this->categoryService->category($request->query())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $categoryId)
    {
        return ShowCategoryResource::make(
            $this->categoryService->category($request->query(), $categoryId)
        );
    }
}
