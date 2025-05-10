<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryServices\CategoryService;
use App\Http\Requests\Api\Admin\Category\CategoryRequest;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return response()->json([
            'success' => __('admin.category.created'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $categoryId)
    {
        $this->categoryService->update($categoryId, $request->validated());

        return response()->json([
            'success' => __('admin.category.updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $categoryId)
    {
        $this->categoryService->delete($categoryId);

        return response()->json([
            'success' => __('admin.category.deleted'),
        ]);
    }
}
