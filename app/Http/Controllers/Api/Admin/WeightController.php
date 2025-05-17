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
        $weights = $this->weightService->weight($request->query());

        return IndexWeightResource::collection($weights);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $weightId)
    {
        $weight = $this->weightService->weight($request->query(), $weightId);

        return ShowWeightResource::make($weight);
    }

    public function store(WeightRequest $request)
    {
        $this->weightService->create($request->validated());

        return response()->json([
            'success' => __('admin.weight.created'),
        ]);
    }

    public function update(WeightRequest $request, string $weightId)
    {
        $this->weightService->update($weightId, $request->validated());

        return response()->json([
            'success' => __('admin.weight.updated'),
        ]);
    }

    public function destroy(string $weightId)
    {
        $this->weightService->delete($weightId);

        return response()->json([
            'success' => __('admin.weight.updated'),
        ]);
    }
}
