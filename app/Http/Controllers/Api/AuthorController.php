<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Author\AuthorResource;
use App\Services\AuthorServices\AuthorService;
use Illuminate\Http\Request;

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
        $authors = $this->authorService->author();

        return AuthorResource::collection($authors);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $authorId)
    {
        $author = $this->authorService->author($authorId);

        return AuthorResource::make($author);
    }
}
