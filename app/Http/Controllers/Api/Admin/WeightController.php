<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WeightServices\WeightService;
use App\Http\Requests\Api\Admin\Weight\WeightRequest;
use App\Http\Resources\Admin\Weight\ShowWeightResource;
use App\Http\Resources\Admin\Weight\IndexWeightResource;

class WeightController extends Controller
{
    public function __construct(public WeightService $weightService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $journals = $this->weightService->weight($request->query());

        return IndexWeightResource::collection($journals);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $journalId)
    {
        $journal = $this->weightService->weight($request->query(), $journalId);

        return ShowWeightResource::make($journal);
    }

    public function store(WeightRequest $request)
    {
        $this->weightService->create($request->validated());

        return response()->json([
            'success' => __('admin.weight.created'),
        ]);
    }

    public function update(WeightRequest $request, string $journalId)
    {
        $this->weightService->update($journalId, $request->validated());

        return response()->json([
            'success' => __('admin.weight.updated'),
        ]);
    }

    public function delete(string $authorId)
    {
        $this->weightService->delete($authorId);

        return response()->json([
            'success' => __('admin.weight.updated'),
        ]);
    }
}
