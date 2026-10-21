<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 15) as $number) {
            $sequence = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

            User::firstOrCreate(
                ['email' => "penonton{$sequence}@example.test"],
                [
                    'name' => "Penonton Demo {$sequence}",
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'user',
                ]
            );
        }
    }
}
