<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $permissions = [
                [
                    'name' => 'role_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'role_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'permission_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'user_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'user_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'publish_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'publish_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'author_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'author_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'journal_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'journal_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'category_create',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'category_edit',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('permissions')->insert($permissions);
        });

        DB::table('roles')->insert(['name' => 'admin', 'guard_name' => 'api']);
        for ($i = 1; $i < 13; $i++) {
            DB::table('role_has_permissions')->insert(['permission_id' => $i, 'role_id' => 1]);
        }
        \App\Models\User::find(1)->assignRole('admin');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
