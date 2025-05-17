<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PublisherServices\PublisherService;
use App\Http\Resources\Admin\Author\ShowAuthorResource;
use App\Http\Resources\Admin\Author\IndexAuthorResource;
use App\Http\Requests\Api\Admin\Publisher\PublisherRequest;

class PublisherController extends Controller
{
    public function __construct(public PublisherService $publisherService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $publishers = $this->publisherService->publisher($request->query());

        return IndexAuthorResource::collection($publishers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $publisherId)
    {
        $publisher = $this->publisherService->publisher($request->query(), $publisherId);

        return ShowAuthorResource::make($publisher);
    }

    public function store(PublisherRequest $request)
    {
        $this->publisherService->create($request->validated());

        return response()->json([
            'success' => __('admin.publisher.created'),
        ]);
    }

    public function update(PublisherRequest $request, string $publisherId)
    {
        $this->publisherService->update($publisherId, $request->validated());

        return response()->json([
            'success' => __('admin.publisher.updated'),
        ]);
    }

    public function destroy(string $publisherId)
    {
        $this->publisherService->delete($publisherId);

        return response()->json([
            'success' => __('admin.publisher.deleted'),
        ]);
    }
}
