<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function reports(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $startDate = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $validated['end_date'] ?? now()->endOfMonth()->toDateString();
        if (Carbon::parse($endDate)->lt(Carbon::parse($startDate))) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            ]);
        }
        $activeOrders = ['pending', 'completed'];

        $showtimes = Showtime::with('film')
            ->whereBetween('starts_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->addSelect([
                'tickets_sold' => DB::table('order_details')
                    ->join('orders', 'orders.id', '=', 'order_details.order_id')
                    ->selectRaw('COALESCE(SUM(order_details.quantity), 0)')
                    ->whereColumn('order_details.showtime_id', 'showtimes.id')
                    ->whereIn('orders.status', $activeOrders),
            ])
            ->addSelect([
                'revenue' => DB::table('order_details')
                    ->join('orders', 'orders.id', '=', 'order_details.order_id')
                    ->selectRaw('COALESCE(SUM(order_details.price * order_details.quantity), 0)')
                    ->whereColumn('order_details.showtime_id', 'showtimes.id')
                    ->whereIn('orders.status', $activeOrders),
            ])
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        $periodSalesQuery = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('showtimes', 'showtimes.id', '=', 'order_details.showtime_id')
            ->whereIn('orders.status', $activeOrders)
            ->whereBetween('showtimes.starts_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        $summary = [
            'orders' => (clone $periodSalesQuery)->distinct('orders.id')->count('orders.id'),
            'revenue' => (clone $periodSalesQuery)->sum(DB::raw('order_details.price * order_details.quantity')),
            'completed' => (clone $periodSalesQuery)->where('orders.status', 'completed')->distinct('orders.id')->count('orders.id'),
            'pending' => (clone $periodSalesQuery)->where('orders.status', 'pending')->distinct('orders.id')->count('orders.id'),
        ];

        return view('admin.reports.index', compact('showtimes', 'summary', 'startDate', 'endDate'));
    }
}
