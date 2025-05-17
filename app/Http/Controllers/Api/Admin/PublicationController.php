<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Publication\PublicationRequest;
use App\Http\Resources\Api\Admin\Publication\ShowPublicationResource;
use App\Http\Resources\Api\Admin\Publication\IndexPublicationResource;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Publication::query();

        $publications = $query->paginate($request->input('per_page', 15));

        return IndexPublicationResource::collection($publications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublicationRequest $request)
    {
        $data = $request->validated();

        $publication = Publication::create($data);

        $publication->authors()->attach($data['author_id']);
        $publication->categories()->attach($data['category_id']);
        $publication->publisher()->associate($data['publisher_id']);

        return response()->json([
            'message' => __('admin.publication.created'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $publicationId)
    {
        $publication = Publication::findOrFail($publicationId);

        return ShowPublicationResource::make($publication);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublicationRequest $request, string $publicationId)
    {
        $data = $request->validated();

        $publication = Publication::findOrFail($publicationId);

        $publication->update($data);

        if (isset($data['author_id'])) {
            $publication->authors()->sync($data['author_id']);
        }
        if (isset($data['category_id'])) {
            $publication->categories()->sync($data['category_id']);
        }
        if (isset($data['publisher_id'])) {
            $publication->publisher()->associate($data['publisher_id']);
        }

        return response()->json([
            'message' => __('admin.publication.updated'),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $publicationId)
    {
        $publication = Publication::findOrFail($publicationId);

        $publication->delete();

        return response()->json([
            'message' => __('admin.publication.deleted'),
        ], 200);
    }
}
