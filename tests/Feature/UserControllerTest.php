<?php

namespace Tests\Feature;

use Database\Seeders\PermissionSeeder;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
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

    public function test_can_view_users_list()
    {
        $this->actingAs($this->admin)->getJson(route('users.index'))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200')
            ]);
    }

    public function test_can_create_user_with_valid_data()
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $this->actingAs($this->admin)->postJson(route('users.store'), $data)
            ->assertStatus(201)
            ->assertJson([
                'message' => __('http-statuses.201'),
            ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_cannot_create_user_without_permission()
    {
        $userWithoutPermission = User::factory()->create();

        $this->actingAs($userWithoutPermission)->postJson(route('users.store'), [
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ])->assertStatus(403);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Updated User',
            'email' => 'updateduser@example.com'
        ];

        $this->actingAs($this->admin)->putJson(route('users.update', $user->id), $data)
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertDatabaseHas('users', ['email' => 'updateduser@example.com']);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)->deleteJson(route('users.destroy', $user->id))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_can_follow_user()
    {
        $userToFollow = User::factory()->create();

        $this->actingAs($this->admin)->postJson(route('users.follow', $userToFollow->id))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertTrue($userToFollow->followers->contains($this->admin));
    }

    public function test_can_unfollow_user()
    {
        $userToUnfollow = User::factory()->create();
        $this->admin->followers()->attach($userToUnfollow->id);

        $this->actingAs($this->admin)->postJson(route('users.unfollow', $userToUnfollow->id))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertFalse($userToUnfollow->followers->contains($this->admin));
    }

    public function test_can_upload_avatar()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->actingAs($this->admin)->postJson(route('users.upload-avatar', $user->id), [
            'avatar' => $file,
        ])
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertNotNull($user->fresh()->avatar);
    }

    public function test_can_remove_avatar()
    {
        $user = User::factory()->create(['avatar' => 'avatar.jpg']);

        $this->actingAs($this->admin)->deleteJson(route('users.remove-avatar', $user->id))
            ->assertStatus(200)
            ->assertJson([
                'message' => __('http-statuses.200'),
            ]);

        $this->assertNull($user->fresh()->avatar);
    }
}
