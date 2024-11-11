<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_login_with_email_password_and_device_name(): void
    {
        $role = Role::create(['name' => 'user', 'guard_name' => 'sanctum']);
        $user = User::factory()->create([
            'password' => bcrypt($password = '123qweasd'),
        ])->assignRole($role);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
            'device_name' => 'testing',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);

        $token = $response->json('token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'roles',
                ],
            ]);
    }

    public function test_login_with_email_password_and_device_name_and_wrong_password(): void
    {
        $role = Role::create(['name' => 'user', 'guard_name' => 'sanctum']);
        $user = User::factory()->create([
            'password' => bcrypt($password = '123qweasd'),
        ])->assignRole($role);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'testing',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_access_protected_route_without_token(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }
}
