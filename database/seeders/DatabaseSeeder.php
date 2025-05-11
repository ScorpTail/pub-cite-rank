<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::find(1)) {
            User::create([
                'first_name' => 'Vlad',
                'last_name' => 'Danchevskyi',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

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
                [
                    'name' => 'admin_panel',
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            $permissions = Permission::all()->filter(function ($permission) use ($permissions) {
                return !in_array($permission->name, array_column($permissions, 'name'));
            })->map(function ($permission) use ($permissions) {
                $permissions[] = [
                    'name' => $permission->name,
                    'guard_name' => 'api',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            Permission::insert($permissions);

            if (!DB::table('roles')->where('name', 'admin')->exists()) {
                DB::table('roles')->insert(['name' => 'admin', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()]);
            }

            $admin = DB::table('roles')->where('name', 'admin')->first();
            DB::table('permissions')->whereIn('name', data_get($permissions, '*.name'))->get()->each(function ($permission) use ($admin) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permission->id, 'role_id' => $admin->id]);
            });
            User::find(1)->assignRole('admin');
        });
    }
}
