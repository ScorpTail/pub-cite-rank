<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Publisher\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Publisher::query();


        $publishers = $query->paginate(15);

        return PublisherResource::collection($publishers);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $publisherId)
    {
        $publication = Publisher::findOrFail($publisherId);

        return PublisherResource::make($publication);
    }
}
