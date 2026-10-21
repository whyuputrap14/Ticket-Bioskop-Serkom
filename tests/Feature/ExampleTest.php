<?php

use App\Models\Film;

it('returns a successful response', function () {
    Film::create([
        'name' => 'Film Publik',
        'category' => 'Drama',
        'price' => 25000,
        'description' => 'Deskripsi film.',
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Film Publik')
        ->assertSee('Login')
        ->assertSee('Register')
        ->assertSee('Login untuk memesan');
});
