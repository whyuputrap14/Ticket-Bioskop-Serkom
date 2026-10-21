<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\OrderDetail;
use App\Models\Showtime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $films = Film::with(['showtimes' => fn ($query) => $query->where('starts_at', '>', now())->orderBy('starts_at')])
            ->orderBy('name')
            ->get();

        return view('customer.index', compact('films'));
    }

    public function customerDashboard()
    {
        $orders = Order::with('orderDetails.film', 'orderDetails.showtime')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        $showtimes = Showtime::with('film')->where('starts_at', '>', now())->orderBy('starts_at')->limit(5)->get();

        return view('customer.dashboard', compact('orders', 'showtimes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $films = Film::with(['showtimes' => fn ($query) => $query->where('starts_at', '>', now())->orderBy('starts_at')])
            ->orderBy('name')->get();
        $selectedQuantities = [];

        return view('admin.orders.create', compact('films', 'selectedQuantities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'seat_number'  => 'required|integer|min:1',
            'items'         => 'required|array',
            'items.*'       => 'nullable|integer|min:0',
            'showtimes' => ['required', 'array'],
            'showtimes.*' => ['nullable', 'integer', 'exists:showtimes,id'],
        ]);

        $orderedItems = array_filter($request->items, fn ($qty) => $qty > 0);

        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu film!');
        }

        $selectedShowtimes = [];
        foreach ($orderedItems as $filmId => $quantity) {
            $showtimeId = $request->input("showtimes.$filmId");
            $showtimeExists = $showtimeId && Showtime::query()
                ->whereKey($showtimeId)
                ->where('film_id', $filmId)
                ->where('starts_at', '>', now())
                ->exists();

            if (! $showtimeExists) {
                throw ValidationException::withMessages([
                    "showtimes.$filmId" => 'Pilih jadwal tayang yang tersedia untuk setiap film yang dipesan.',
                ]);
            }

            $selectedShowtimes[$filmId] = $showtimeId;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'seat_number'  => $request->seat_number,
                'total_price'   => 0,
                'status'        => 'pending',
            ]);

            $totalPrice = 0;

            foreach ($orderedItems as $filmId => $quantity) {
                $film = Film::findOrFail($filmId);
                $subtotal = $film->price * $quantity;
                $totalPrice += $subtotal;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'film_id'  => $film->id,
                    'showtime_id' => $selectedShowtimes[$film->id],
                    'quantity' => $quantity,
                    'price'    => $film->price,
                ]);
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();

            return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat! Nomor Kursi: ' . $order->seat_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load('orderDetails');
        $films = Film::with(['showtimes' => fn ($query) => $query->where('starts_at', '>', now())->orderBy('starts_at')])
            ->orderBy('name')->get();
        $selectedQuantities = $order->orderDetails->pluck('quantity', 'film_id')->all();
        $selectedShowtimes = $order->orderDetails->pluck('showtime_id', 'film_id')->all();

        return view('admin.orders.edit', compact('order', 'films', 'selectedQuantities', 'selectedShowtimes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $this->validateAdminOrder($request);
        $items = collect($validated['items'])->filter(fn ($item) => $item['quantity'] > 0)->values();

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => 'Pilih minimal satu film.'])->withInput();
        }

        $this->validateOrderShowtimes($items);

        DB::transaction(function () use ($order, $validated, $items) {
            $order->update([
                'customer_name' => $validated['customer_name'],
                'seat_number' => $validated['seat_number'],
                'status' => $validated['status'],
            ]);
            $order->orderDetails()->delete();
            $order->update(['total_price' => $this->saveOrderItems($order, $items)]);
        });

        return redirect()->route('admin.dashboard')->with('success', 'Pesanan #' . $order->id . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Pesanan #' . $order->id . ' berhasil dihapus.');
    }

    public function adminDashboard()
    {
        $orders = Order::with('orderDetails.film', 'orderDetails.showtime')->latest()->paginate(10);
        return view('dashboard', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAdminOrder($request);
        $items = collect($validated['items'])->filter(fn ($item) => $item['quantity'] > 0)->values();

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => 'Pilih minimal satu film.'])->withInput();
        }

        $this->validateOrderShowtimes($items);

        $order = DB::transaction(function () use ($validated, $items) {
            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'seat_number' => $validated['seat_number'],
                'total_price' => 0,
                'status' => $validated['status'],
            ]);
            $order->update(['total_price' => $this->saveOrderItems($order, $items)]);

            return $order;
        });

        return redirect()->route('admin.dashboard')->with('success', 'Pesanan #' . $order->id . ' berhasil ditambahkan.');
    }

    private function validateAdminOrder(Request $request): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'seat_number' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.film_id' => ['required', 'integer', 'distinct', 'exists:films,id'],
            'items.*.showtime_id' => ['nullable', 'integer', 'distinct', 'exists:showtimes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function validateOrderShowtimes(Collection $items): void
    {
        foreach ($items as $item) {
            $valid = ! empty($item['showtime_id']) && Showtime::query()
                ->whereKey($item['showtime_id'])
                ->where('film_id', $item['film_id'])
                ->exists();

            if (! $valid) {
                throw ValidationException::withMessages([
                    'items' => 'Pastikan setiap film dipasangkan dengan jadwal tayang yang sesuai.',
                ]);
            }
        }
    }

    private function saveOrderItems(Order $order, Collection $items): int
    {
        $films = Film::whereIn('id', $items->pluck('film_id'))->get()->keyBy('id');
        $totalPrice = 0;

        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            if ($quantity === 0) {
                continue;
            }

            $film = $films->get($item['film_id']);
            $subtotal = $film->price * $quantity;
            $totalPrice += $subtotal;

            OrderDetail::create([
                'order_id' => $order->id,
                'film_id' => $film->id,
                'showtime_id' => $item['showtime_id'],
                'quantity' => $quantity,
                'price' => $film->price,
            ]);
        }

        return $totalPrice;
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
        ]);
        $order->update($validated);

        return back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }
}
