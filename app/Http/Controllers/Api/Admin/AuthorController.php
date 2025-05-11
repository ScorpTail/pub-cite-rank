<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthorServices\AuthorService;
use App\Http\Requests\Api\Admin\Author\AuthorRequest;
use App\Http\Resources\Admin\Author\ShowAuthorResource;
use App\Http\Resources\Admin\Author\IndexAuthorResource;

class AuthorController extends Controller
{
    public function __construct(
        public AuthorService $authorService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authors = $this->authorService->author($request->query());

        return IndexAuthorResource::collection($authors);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $authorId)
    {
        $author = $this->authorService->author($request->query(), $authorId);

        return ShowAuthorResource::make($author);
    }

    public function store(AuthorRequest $request)
    {
        $this->authorService->create($request->validated());

        return response()->json([
            'success' => __('admin.author.created'),
        ]);
    }

    public function update(AuthorRequest $request, string $authorId)
    {
        $this->authorService->update($authorId, $request->validated());

        return response()->json([
            'success' => __('admin.author.updated'),
        ]);
    }

    public function delete(string $authorId)
    {
        $this->authorService->delete($authorId);

        return response()->json([
            'success' => __('admin.author.updated'),
        ]);
    }
}
