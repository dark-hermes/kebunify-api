<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
                    '*',
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

            if ($role['permissions']) {
                if ($role['permissions'][0] === '*') {
                    $newRole->givePermissionTo(Permission::all());
                } else {
                    $newRole->givePermissionTo($role['permissions']);
                }
            }
        }
    }
}
