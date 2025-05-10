<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryServices\CategoryService;
use Illuminate\Http\Request;

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
        return CategoryResource::collection(
            $this->categoryService->category($request->query())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $categoryId)
    {
        return CategoryResource::make(
            $this->categoryService->category($request->query(), $categoryId)
        );
    }
}
