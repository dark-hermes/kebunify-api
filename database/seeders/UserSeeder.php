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
                'role' => 'user',
                'users' => [
                    'seller1@mail.test',
                    'seller2@mail.test',
                ],
            ],
            [
                'role' => 'user',
                'users' => [
                    'expert1@mail.test',
                    'expert2@mail.test',
                    'expert3@mail.test',
                    'expert4@mail.test',
                    'expert5@mail.test',
                    'expert6@mail.test',
                    'expert7@mail.test',
                    'expert8@mail.test',
                    'expert9@mail.test'
                ],
            ]
        ];

        foreach ($users as $user) {
            $role = $user['role'];
            $theUsers = $user['users'];

            // $newUser = User::factory()->create([
            //     'email' => $users[0]
            // ]);
            // $newUser->assignRole($role);

            // $newUserUnv = User::factory()->unverified()->create([
            //     'email' => $users[1]
            // ]);
            // $newUserUnv->assignRole($role);

            foreach ($theUsers as $theUser) {
                $newUser = User::factory()->create([
                    'email' => $theUser
                ]);
                $newUser->assignRole($role);
            }
        }

        User::factory(20)->create();
    }
}
