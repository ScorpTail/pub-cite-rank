<?php

namespace App\Services\RoleSerivces;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function role(array $param = [], ?int $roleId = null)
    {
        return $roleId
            ? $this->getRole($roleId, $param)
            : $this->getRoles($param);
    }

    private function getRoles(array $param = [])
    {
        $query = Role::query();

        if (isset($param['name'])) {
            $query->where('name', 'like', '%' . $param['name'] . '%');
        }

        return $query->paginate(15);
    }

    private function getRole(int $roleId, array $param = [])
    {
        $query = Role::query();

        $query->where('id', $roleId);

        return $query->firstOrFail();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create($data);

            $role->syncPermissions($data['permissions'] ?? []);
        });
    }

    public function update(string $roleId, array $data)
    {
        return DB::transaction(function () use ($roleId, $data) {
            $role = Role::find($roleId);
            $role->update($data);

            $role->syncPermissions($data['permissions'] ?? []);
        });
    }

    public function delete(string $roleId)
    {
        return DB::transaction(function () use ($roleId) {
            return Role::where('id', $roleId)->delete();
        });
    }
}
