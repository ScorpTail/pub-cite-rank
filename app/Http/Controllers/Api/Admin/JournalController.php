<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\JournalServices\JournalService;
use App\Http\Requests\Api\Admin\Journal\JournalRequest;
use App\Http\Resources\Admin\Author\ShowAuthorResource;
use App\Http\Resources\Admin\Author\IndexAuthorResource;

class JournalController extends Controller
{
    public function __construct(public JournalService $journalService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $journals = $this->journalService->journal($request->query());

        return IndexAuthorResource::collection($journals);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $journalId)
    {
        $journal = $this->journalService->journal($request->query(), $journalId);

        return ShowAuthorResource::make($journal);
    }

    public function store(JournalRequest $request)
    {
        $this->journalService->create($request->validated());

        return response()->json([
            'success' => __('admin.journal.created'),
        ]);
    }

    public function update(JournalRequest $request, string $journalId)
    {
        $this->journalService->update($journalId, $request->validated());

        return response()->json([
            'success' => __('admin.journal.updated'),
        ]);
    }

    public function delete(string $authorId)
    {
        $this->journalService->delete($authorId);

        return response()->json([
            'success' => __('admin.journal.updated'),
        ]);
    }
}
