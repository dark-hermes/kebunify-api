<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create',
            'view',
            'update',
            'delete',
        ];

        $models = [
            'role',
            'user',
            'expert',
            'expert_specialization',
            'tag',
            'article',
        ];

        $specificPermissions = [
            //
        ];

        foreach ($models as $model) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission . '_' . $model,
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        foreach ($specificPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}
