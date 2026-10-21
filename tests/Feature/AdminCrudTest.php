<?php

use App\Models\Film;
use App\Models\Order;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests cannot access admin film management', function () {
    $this->get(route('admin.films.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can create update and delete films', function () {
    $this->actingAs(User::factory()->admin()->create());

    $filmData = [
        'name' => 'Film Pertama',
        'category' => 'Drama',
        'price' => 25000,
        'description' => 'Deskripsi film.',
    ];

    $this->post(route('admin.films.store'), $filmData)
        ->assertRedirect(route('admin.films.index'))
        ->assertSessionHasNoErrors();

    $film = Film::where('name', 'Film Pertama')->firstOrFail();
    $this->assertDatabaseHas('films', ['id' => $film->id, 'price' => 25000]);

    $this->put(route('admin.films.update', $film), array_merge($filmData, [
        'name' => 'Film Diperbarui',
        'price' => 30000,
    ]))
        ->assertRedirect(route('admin.films.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('films', ['id' => $film->id, 'name' => 'Film Diperbarui', 'price' => 30000]);

    $this->delete(route('admin.films.destroy', $film))
        ->assertRedirect(route('admin.films.index'));

    $this->assertDatabaseMissing('films', ['id' => $film->id]);
});

test('checkout stores the film unit price in the order detail', function () {
    $film = Film::create([
        'name' => 'Film Checkout',
        'category' => 'Aksi',
        'price' => 27000,
        'description' => 'Deskripsi film.',
    ]);
    $showtime = Showtime::create([
        'film_id' => $film->id,
        'starts_at' => now()->addDay()->startOfHour(),
        'hall' => 'Studio 1',
    ]);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('customer.checkout'), [
        'customer_name' => 'Pelanggan',
        'seat_number' => 1,
        'items' => [$film->id => 2],
        'showtimes' => [$film->id => $showtime->id],
    ])->assertRedirect(route('customer.index'))
        ->assertSessionHasNoErrors();

    $order = Order::firstOrFail();
    $this->assertSame(54000, $order->total_price);
    $this->assertSame($user->id, $order->user_id);
    $this->assertDatabaseHas('order_details', [
        'order_id' => $order->id,
        'film_id' => $film->id,
        'quantity' => 2,
        'price' => 27000,
        'showtime_id' => $showtime->id,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Jadwal Tayang')
        ->assertSee('Pemesanan Tiket')
        ->assertSee('Registrasi')
        ->assertSee('Laporan')
        ->assertSee('Film Checkout')
        ->assertSee('54,000');

    $this->patch(route('admin.orders.updateStatus', $order), ['status' => 'completed'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);

    $this->actingAs($user)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Film Checkout');

    $this->get(route('customer.index'))
        ->assertOk()
        ->assertSee('Film Checkout')
        ->assertSee('Jadwal Tayang');
});

test('admin can create edit and delete an order', function () {
    $this->actingAs(User::factory()->admin()->create());

    $film = Film::create([
        'name' => 'Film Pesanan Admin',
        'category' => 'Drama',
        'price' => 30000,
        'description' => 'Deskripsi film.',
    ]);
    $showtime = Showtime::create([
        'film_id' => $film->id,
        'starts_at' => now()->addDay()->startOfHour(),
        'hall' => 'Studio 2',
    ]);

    $this->get(route('admin.orders.create'))
        ->assertOk()
        ->assertSee('Tambah Pesanan');

    $orderData = [
        'customer_name' => 'Pelanggan Admin',
        'seat_number' => 5,
        'status' => 'pending',
        'items' => [
            ['film_id' => $film->id, 'showtime_id' => $showtime->id, 'quantity' => 2],
        ],
    ];

    $this->post(route('admin.orders.store'), $orderData)
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHasNoErrors();

    $order = Order::where('customer_name', 'Pelanggan Admin')->firstOrFail();
    $this->assertSame(60000, $order->total_price);
    $this->assertDatabaseHas('order_details', [
        'order_id' => $order->id,
        'film_id' => $film->id,
        'quantity' => 2,
        'price' => 30000,
    ]);

    $this->get(route('admin.orders.edit', $order))
        ->assertOk()
        ->assertSee('Edit Pesanan');

    $this->put(route('admin.orders.update', $order), array_merge($orderData, [
        'customer_name' => 'Pelanggan Diubah',
        'status' => 'completed',
        'items' => [
            ['film_id' => $film->id, 'showtime_id' => $showtime->id, 'quantity' => 1],
        ],
    ]))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'customer_name' => 'Pelanggan Diubah',
        'status' => 'completed',
        'total_price' => 30000,
    ]);
    $this->assertDatabaseHas('order_details', [
        'order_id' => $order->id,
        'film_id' => $film->id,
        'quantity' => 1,
        'price' => 30000,
    ]);

    $this->delete(route('admin.orders.destroy', $order))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    $this->assertDatabaseMissing('order_details', ['order_id' => $order->id]);
});

test('guests cannot use admin order management', function () {
    $this->get(route('admin.orders.create'))
        ->assertRedirect(route('login'));
});

test('registration creates a user and only admins may access admin pages', function () {
    $this->post(route('register'), [
        'name' => 'User Baru',
        'email' => 'userbaru@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard'));

    $user = User::where('email', 'userbaru@example.com')->firstOrFail();
    $this->assertSame('user', $user->role);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'user']);

    $this->actingAs($user);
    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('dashboard'))->assertRedirect(route('customer.dashboard'));
    $this->get(route('customer.dashboard'))->assertOk();
});

test('admin showtime management and reports are available to admins only', function () {
    $film = Film::create([
        'name' => 'Film Jadwal',
        'category' => 'Aksi',
        'price' => 25000,
        'description' => 'Deskripsi film.',
    ]);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.showtimes.create'))
        ->assertOk();

    $this->post(route('admin.showtimes.store'), [
        'film_id' => $film->id,
        'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        'hall' => 'Studio 1',
    ])->assertRedirect(route('admin.showtimes.index'))
        ->assertSessionHasNoErrors();

    $showtime = Showtime::firstOrFail();
    $this->get(route('admin.showtimes.index'))->assertOk()->assertSee('Film Jadwal');
    $this->get(route('admin.showtimes.edit', $showtime))->assertOk();
    $this->put(route('admin.showtimes.update', $showtime), [
        'film_id' => $film->id,
        'starts_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
        'hall' => 'Studio 2',
    ])->assertRedirect(route('admin.showtimes.index'))
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('showtimes', ['id' => $showtime->id, 'hall' => 'Studio 2']);

    $this->get(route('admin.reports.index'))->assertOk();
    $this->get(route('admin.registrations.index'))->assertOk();

    $this->delete(route('admin.showtimes.destroy', $showtime))
        ->assertRedirect(route('admin.showtimes.index'));
    $this->assertDatabaseMissing('showtimes', ['id' => $showtime->id]);
});

test('admin can manage audience registration records', function () {
    $this->actingAs(User::factory()->admin()->create());

    $audienceData = [
        'name' => 'Penonton Baru',
        'email' => 'penonton.baru@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->get(route('admin.registrations.create'))
        ->assertOk()
        ->assertSee('Tambah Penonton');

    $this->post(route('admin.registrations.store'), $audienceData)
        ->assertRedirect(route('admin.registrations.index'))
        ->assertSessionHasNoErrors();

    $audience = User::where('email', $audienceData['email'])->firstOrFail();
    $this->assertSame('user', $audience->role);
    $this->assertTrue(Hash::check('password123', $audience->password));
    $this->get(route('admin.registrations.index'))
        ->assertOk()
        ->assertSee($audienceData['email']);

    $this->put(route('admin.registrations.update', $audience), [
        'name' => 'Penonton Diperbarui',
        'email' => 'penonton.update@example.test',
        'password' => '',
        'password_confirmation' => '',
        'role' => 'admin',
    ])->assertRedirect(route('admin.registrations.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $audience->id,
        'name' => 'Penonton Diperbarui',
        'email' => 'penonton.update@example.test',
        'role' => 'user',
    ]);

    $this->delete(route('admin.registrations.destroy', $audience))
        ->assertRedirect(route('admin.registrations.index'));
    $this->assertDatabaseMissing('users', ['id' => $audience->id]);
});

test('reports filter films and ticket totals by showtime period', function () {
    $admin = User::factory()->admin()->create();
    $audience = User::factory()->create();
    $film = Film::create([
        'name' => 'Film Laporan',
        'category' => 'Drama',
        'price' => 25000,
        'description' => 'Film untuk laporan.',
    ]);
    $periodStart = now()->startOfMonth();
    $showtime = Showtime::create([
        'film_id' => $film->id,
        'starts_at' => $periodStart->copy()->addDays(2)->setTime(19, 0),
        'hall' => 'Studio Laporan',
    ]);
    $outsideShowtime = Showtime::create([
        'film_id' => $film->id,
        'starts_at' => $periodStart->copy()->subMonth()->addDays(2)->setTime(19, 0),
        'hall' => 'Studio Lama',
    ]);
    $order = Order::create([
        'user_id' => $audience->id,
        'customer_name' => $audience->name,
        'seat_number' => 10,
        'total_price' => 50000,
        'status' => 'completed',
    ]);
    $order->orderDetails()->create([
        'film_id' => $film->id,
        'showtime_id' => $showtime->id,
        'quantity' => 2,
        'price' => 25000,
    ]);
    $outsideOrder = Order::create([
        'user_id' => $audience->id,
        'customer_name' => $audience->name,
        'seat_number' => 11,
        'total_price' => 25000,
        'status' => 'completed',
    ]);
    $outsideOrder->orderDetails()->create([
        'film_id' => $film->id,
        'showtime_id' => $outsideShowtime->id,
        'quantity' => 1,
        'price' => 25000,
    ]);
    $cancelledOrder = Order::create([
        'user_id' => $audience->id,
        'customer_name' => $audience->name,
        'seat_number' => 12,
        'total_price' => 100000,
        'status' => 'cancelled',
    ]);
    $cancelledOrder->orderDetails()->create([
        'film_id' => $film->id,
        'showtime_id' => $showtime->id,
        'quantity' => 4,
        'price' => 25000,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.index', [
            'start_date' => $periodStart->toDateString(),
            'end_date' => $periodStart->copy()->endOfMonth()->toDateString(),
        ]))
        ->assertOk()
        ->assertSee('Film Laporan')
        ->assertSee('Studio Laporan')
        ->assertSee('50,000')
        ->assertSee('p-4">2</td>', false)
        ->assertDontSee('Studio Lama');
});

test('audience demo seeder creates at least 15 audience accounts idempotently', function () {
    $this->seed(\Database\Seeders\AudienceSeeder::class);
    $this->seed(\Database\Seeders\AudienceSeeder::class);

    $this->assertSame(15, User::where('email', 'like', 'penonton%@example.test')->count());
    $this->assertSame(15, User::where('role', 'user')->count());
});

test('admin and user logins redirect to their own dashboards', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin-role@example.test',
        'password' => Hash::make('password'),
    ]);

    $this->post(route('login'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
    $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));

    $this->post(route('logout'));

    $user = User::factory()->create([
        'email' => 'user-role@example.test',
        'password' => Hash::make('password'),
    ]);
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
    $this->get(route('dashboard'))->assertRedirect(route('customer.dashboard'));
});
