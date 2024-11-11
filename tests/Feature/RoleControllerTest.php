<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions if required
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        // Create a user with necessary roles and permissions
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin'); // Assuming 'admin' role has all permissions
    }

    public function test_can_view_roles_list()
    {
        $this->actingAs($this->admin)->getJson(route('roles.index'))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200')
            ]);
    }

    public function test_can_create_role_with_valid_data()
    {
        $data = [
            'name' => 'New Role',
            'permissions' => [
                'create_article',
                'update_article',
                'delete_article',
                'view_article',
            ]
        ];

        $this->actingAs($this->admin)->postJson(route('roles.store'), $data)
            ->assertStatus(201)
            ->assertJson([
                'message' => __('http-statuses.201'),
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'New Role']);
    }

    public function test_cannot_create_role_without_permission()
    {
        $userWithoutPermission = User::factory()->create();

        $data = [
            'name' => 'New Role',
            'permissions' => [
                'create_article',
                'update_article',
                'delete_article',
                'view_article',
            ]
        ];

        $this->actingAs($userWithoutPermission)->postJson(route('roles.store'), $data)
            ->assertStatus(403);
    }

    public function test_can_update_role()
    {
        $role = Role::create(['name' => 'Old Role', 'guard_name' => 'sanctum']);

        $data = [
            'name' => 'Updated Role',
            'permissions' => [
                'create_article',
                'update_article',
                'delete_article',
                'view_article',
            ]
        ];

        $this->actingAs($this->admin)->putJson(route('roles.update', $role->id), $data)
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'Updated Role']);
    }

    public function test_can_delete_role()
    {
        $role = Role::create(['name' => 'Role to be deleted', 'guard_name' => 'sanctum']);

        $this->actingAs($this->admin)->deleteJson(route('roles.destroy', $role->id))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertSoftDeleted('roles', ['name' => 'Role to be deleted', 'guard_name' => 'sanctum']);
    }
}
