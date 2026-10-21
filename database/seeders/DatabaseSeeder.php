<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin Default
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Bioskop',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Panggil Seeder Film
        $this->call([
            FilmSeeder::class,
            AudienceSeeder::class,
        ]);
    }
}
