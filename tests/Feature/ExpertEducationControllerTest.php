<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Expert;
use App\Models\ExpertEducation;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ExpertSpecializationSeeder;
use Database\Seeders\ExpertSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ExpertEducationControllerTest extends TestCase
{
//     use RefreshDatabase;

//     private $admin;
//     private $expert;

//     protected function setUp(): void
//     {
//         parent::setUp();

//         // Seed necessary data
//         $this->seed(PermissionSeeder::class);
//         $this->seed(RoleSeeder::class);
//         $this->seed(UserSeeder::class);
//         $this->seed(ExpertSpecializationSeeder::class);
//         $this->seed(ExpertSeeder::class);

//         // Create a user with 'admin' role and necessary permissions
//         $this->admin = User::first(); // Assuming the first user is admin after seeding
//         $this->admin->assignRole('admin'); // Assign 'admin' role if not already assigned

//         // Create an Expert for testing purposes
//         $this->expert = Expert::first(); // Assuming ExpertSeeder creates an expert
//     }

//     public function test_index_returns_all_expert_educations()
//     {
//         $expertEducation = ExpertEducation::factory()->create(['expert_id' => $this->expert->id]);

//         $this->actingAs($this->admin)->getJson("/api/experts/{$this->expert->id}/educations")
//             ->assertStatus(200)
//             ->assertJson([
//                 'message' => __('http-statuses.200'),
//                 'data' => [
//                     [
//                         'id' => $expertEducation->id,
//                         'expert_id' => $expertEducation->expert_id,
//                         'degree' => $expertEducation->degree,
//                         'institution' => $expertEducation->institution,
//                         'graduation_year' => $expertEducation->graduation_year,
//                         'field_of_study' => $expertEducation->field_of_study,
//                     ],
//                 ],
//             ]);
//     }

//     public function test_store_creates_new_expert_education()
//     {
//         $data = [
//             'degree' => 'Bachelor of Science',
//             'institution' => 'University of Science',
//             'graduation_year' => '2022',
//             'field_of_study' => 'Agriculture',
//         ];

//         $this->actingAs($this->admin)->postJson("/api/experts/{$this->expert->id}/educations", $data)
//             ->assertStatus(201)
//             ->assertJson([
//                 'message' => __('http-statuses.201'),
//                 'data' => $data
//             ]);

//         $this->assertDatabaseHas('expert_educations', array_merge($data, ['expert_id' => $this->expert->id]));
//     }

//     public function test_store_with_authenticated_user_creates_new_expert_education()
//     {
//         $user = User::factory()->create();
//         Auth::login($user);

//         $data = [
//             'degree' => 'Master of Science',
//             'institution' => 'Advanced University',
//             'graduation_year' => '2023',
//             'field_of_study' => 'Farming Technology',
//         ];

//         $this->actingAs($user)->postJson('/api/experts/auth/educations', $data)
//             ->assertStatus(201)
//             ->assertJson([
//                 'message' => __('http-statuses.201'),
//                 'data' => $data
//             ]);

//         $this->assertDatabaseHas('expert_educations', [
//             'expert_id' => $user->id,
//             'degree' => $data['degree'],
//             'institution' => $data['institution'],
//         ]);
//     }

//     public function test_show_returns_specified_expert_education()
//     {
//         $expertEducation = ExpertEducation::factory()->create(['expert_id' => $this->expert->id]);

//         $this->actingAs($this->admin)->getJson("/api/experts/{$this->expert->id}/educations/{$expertEducation->id}")
//             ->assertStatus(200)
//             ->assertJson([
//                 'message' => __('http-statuses.200'),
//                 'data' => [
//                     'id' => $expertEducation->id,
//                     'expert_id' => $expertEducation->expert_id,
//                     'degree' => $expertEducation->degree,
//                     'institution' => $expertEducation->institution,
//                     'graduation_year' => $expertEducation->graduation_year,
//                     'field_of_study' => $expertEducation->field_of_study,
//                 ],
//             ]);
//     }

//     public function test_update_updates_expert_education()
//     {
//         $expertEducation = ExpertEducation::factory()->create(['expert_id' => $this->expert->id]);

//         $data = [
//             'degree' => 'PhD in Agriculture',
//             'institution' => 'Global University',
//             'graduation_year' => '2024',
//             'field_of_study' => 'Crop Science',
//         ];

//         $this->actingAs($this->admin)->putJson("/api/experts/{$this->expert->id}/educations/{$expertEducation->id}", $data)
//             ->assertStatus(200)
//             ->assertJson([
//                 'message' => __('http-statuses.200'),
//                 'data' => $data
//             ]);

//         $this->assertDatabaseHas('expert_educations', $data);
//     }

//     public function test_destroy_deletes_expert_education()
//     {
//         $expertEducation = ExpertEducation::factory()->create(['expert_id' => $this->expert->id]);

//         $this->actingAs($this->admin)->deleteJson("/api/experts/{$this->expert->id}/educations/{$expertEducation->id}")
//             ->assertStatus(200)
//             ->assertJson([
//                 'message' => __('http-statuses.200'),
//             ]);

//         $this->assertDatabaseMissing('expert_educations', [
//             'id' => $expertEducation->id,
//         ]);
//     }

//     public function test_destroy_with_authenticated_user_deletes_expert_education()
//     {
//         $user = User::factory()->create();
//         Auth::login($user);

//         $expertEducation = ExpertEducation::factory()->create(['expert_id' => $user->id]);

//         $this->actingAs($user)->deleteJson("/api/experts/auth/educations/{$expertEducation->id}")
//             ->assertStatus(200)
//             ->assertJson([
//                 'message' => __('http-statuses.200'),
//             ]);

//         $this->assertDatabaseMissing('expert_educations', [
//             'id' => $expertEducation->id,
//         ]);
//     }

//     public function test_store_validation_error()
//     {
//         $data = [
//             'degree' => '',  // missing required field
//             'institution' => '',
//             'graduation_year' => 'invalid',  // incorrect format
//             'field_of_study' => '',
//         ];

//         $this->actingAs($this->admin)->postJson("/api/experts/{$this->expert->id}/educations", $data)
//             ->assertStatus(422)
//             ->assertJsonValidationErrors(['degree', 'institution', 'graduation_year', 'field_of_study']);
//     }
}
