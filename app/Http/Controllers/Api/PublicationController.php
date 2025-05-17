<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publication\PublicationResource;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Publication::query();

        if ($request->has('published_at')) {
            $query->where('published_at', '>=', $request->input('published_at'));
        }

        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->input('category_id'));
            });
        }

        if ($request->has('publisher_id')) {
            $query->where('publisher_id', $request->input('publisher_id'));
        }

        $publications = $query->paginate(15);

        return PublicationResource::collection($publications);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $publicationId)
    {
        $publication = Publication::findOrFail($publicationId);

        return PublicationResource::make($publication);
    }
}
