<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Weight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
                'email' => 'test@gmail.com',
                'password' => bcrypt('password'),
            ]);
        }

        DB::transaction(function () {
            $permissions = [
                'role_create',
                'role_edit',
                'permission_edit',
                'user_create',
                'user_edit',
                'publish_create',
                'publish_edit',
                'author_create',
                'author_edit',
                'publisher_create',
                'publisher_edit',
                'category_create',
                'category_edit',
                'weight_create',
                'weight_edit',
                'admin_panel',
            ];

            $existsPermissions = Permission::get('name')->pluck('name');

            $createPermissions = [];
            foreach ($permissions as $permission) {
                if (!$existsPermissions->contains($permission)) {
                    $createPermissions[] = [
                        'name' => $permission,
                        'guard_name' => 'api',
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                }
            }

            Permission::insert($createPermissions);

            if (!DB::table('roles')->where('name', 'admin')->exists()) {
                DB::table('roles')->insert(['name' => 'admin', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()]);
            }

            $admin = DB::table('roles')->where('name', 'admin')->first();
            DB::table('permissions')->whereIn('name', data_get($createPermissions, '*.name'))->get()->each(function ($permission) use ($admin) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permission->id, 'role_id' => $admin->id]);
            });
        });

        Weight::insert([
            ['key' => 'publications', 'value' => 1],
            ['key' => 'citations', 'value' => 1],
            ['key' => 'h_index', 'value' => 1],
            ['key' => 'publisher', 'value' => 1],
        ]);

        $user = User::where('email', 'test@gmail.com')->first();

        DB::table('model_has_roles')->insert([
            'role_id' => Role::where('name', 'admin')->first()?->id,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
        ]);

        Artisan::call('app:import-openalex', [
            'type' => 'publishers',
        ]);
    }
}
