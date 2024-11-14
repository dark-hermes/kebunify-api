<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role('user')->inRandomOrder()->take(10)->get();
        foreach ($users as $user) {
            $user->documents()->create([
                'role_applied' => 'expert',
                'document_path' => 'https://api.kebunify.live/storage/documents/1731510655_DSA.pdf',
            ]);
        }

        $users = User::role('user')->whereNotIn('id', $users->pluck('id'))->inRandomOrder()->take(10)->get();
        foreach ($users as $user) {
            $user->documents()->create([
                'role_applied' => 'seller',
                'document_path' => 'https://api.kebunify.live/storage/documents/1731510655_DSA.pdf',
            ]);
        }
    }
}
