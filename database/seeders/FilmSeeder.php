<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = [
            [
                'name' => 'Ghost in the Shell',
                'category' => 'Aksi',
                'price' => 27000,
                'description' => 'Mengisahkan tentang narapidana yang ada didalam penjara, kemudian diteror hantu yang mengincar orang sedang marah, dan memberantas kejahatan serta korupsi',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tukang Bubur Naik Haji',
                'category' => 'Drama',
                'price' => 25000,
                'description' => 'Film drama tentang seorang tukang bubur yang berjuang untuk menunaikan ibadah haji.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Agak Laen',
                'category' => 'Komedi',
                'price' => 20000,
                'description' => 'Film komedi yang penuh dengan tawa dan kegembiraan.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($films as $film) {
            Film::firstOrCreate(
                ['name' => $film['name']],
                $film
            );
        }
    }
}
