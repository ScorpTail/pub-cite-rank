<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Role\RoleRequest;
use App\Services\RoleSerivces\RoleService;
use App\Http\Resources\Admin\Role\IndexRoleResource;
use App\Http\Resources\Admin\Role\ShowRoleResource;

class RoleController extends Controller
{
    public function __construct(public RoleService $roleService) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return IndexRoleResource::collection(
            $this->roleService->role($request->query())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $this->roleService->create($request->validated());

        return response()->json([
            'success' => __('admin.role.created'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $roleId)
    {
        return ShowRoleResource::make(
            $this->roleService->role($request->query(), $roleId)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $roleId)
    {
        $this->roleService->update($roleId, $request->validated());

        return response()->json([
            'success' => __('admin.role.updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $roleId)
    {
        $this->roleService->delete($roleId);

        return response()->json([
            'success' => __('admin.role.deleted'),
        ]);
    }
}
