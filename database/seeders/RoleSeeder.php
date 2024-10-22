<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'permissions' => [
                    'create_role',
                    'view_role',
                    'update_role',
                    'delete_role',
                    'create_user',
                    'view_user',
                    'update_user',
                    'delete_user',
                ]
            ],
            [
                'name' => 'user',
                'permissions' => [
                    //
                ]
            ],
            [
                'name' => 'seller',
                'permissions' => [
                    //
                ]
            ],
            [
                'name' => 'expert',
                'permissions' => [
                    //
                ]
            ]
        ];

        foreach ($roles as $role) {
            $newRole = Role::create([
                'name' => $role['name'],
                'guard_name' => 'sanctum',
            ]);

            $newRole->givePermissionTo($role['permissions']);
        }
    }
}
