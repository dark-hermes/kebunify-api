<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'role' => 'admin',
                'users' => [
                    'admin@mail.test',
                    'admin2@mail.test'
                ]
            ],
            [
                'role' => 'user',
                'users' => [
                    'user1@mail.test',
                    'user2@mail.test',
                ],
            ],
            [
                'role' => 'seller',
                'users' => [
                    'seller1@mail.test',
                    'seller2@mail.test',
                ],
            ],
            [
                'role' => 'expert',
                'users' => [
                    'expert1@mail.test',
                    'expert2@mail.test',
                ],
            ]
        ];

        foreach ($users as $user) {
            $role = $user['role'];
            $users = $user['users'];

            User::factory()->create([
                'email' => $users[0]
            ])
                ->each(function ($user) use ($role) {
                    $user->assignRole($role);
                });

            User::factory()->unverified()->create([
                'email' => $users[1]
            ])
                ->each(function ($user) use ($role) {
                    $user->assignRole($role);
                });
        }

        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
