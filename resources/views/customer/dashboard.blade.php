<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard User</h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('customer.index') }}" class="rounded-lg border bg-white p-6 shadow-sm hover:border-blue-400">
                <h3 class="text-lg font-bold text-gray-800">Jadwal Tayang</h3>
                <p class="mt-1 text-sm text-gray-500">Lihat film dan jadwal yang tersedia.</p>
            </a>
            <a href="{{ route('customer.booking') }}#orderForm" class="rounded-lg border bg-white p-6 shadow-sm hover:border-blue-400">
                <h3 class="text-lg font-bold text-gray-800">Pemesanan Tiket</h3>
                <p class="mt-1 text-sm text-gray-500">Pilih jadwal dan pesan tiket bioskop.</p>
            </a>
        </div>

        <section class="mb-8 rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Jadwal Tayang Terdekat</h3>
            <div class="divide-y">
                @forelse($showtimes as $showtime)
                    <div class="flex flex-wrap justify-between gap-2 py-3">
                        <span class="font-semibold">{{ $showtime->film->name }}</span>
                        <span class="text-gray-600">{{ $showtime->starts_at->format('d-m-Y H:i') }} · {{ $showtime->hall }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada jadwal tayang tersedia.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Riwayat Pemesanan Saya</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 uppercase text-gray-600">
                        <tr><th class="p-3">Pesanan</th><th class="p-3">Jadwal/Film</th><th class="p-3">Kursi</th><th class="p-3">Total</th><th class="p-3">Status</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($orders as $order)
                            <tr>
                                <td class="p-3">#{{ $order->id }}</td>
                                <td class="p-3">
                                    @foreach($order->orderDetails as $detail)
                                        <div>{{ $detail->film->name ?? 'Film' }} x{{ $detail->quantity }}
                                            @if($detail->showtime)
                                                <span class="block text-xs text-gray-500">{{ $detail->showtime->starts_at->format('d-m-Y H:i') }} · {{ $detail->showtime->hall }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td class="p-3">{{ $order->seat_number }}</td>
                                <td class="p-3 font-semibold">Rp {{ number_format($order->total_price) }}</td>
                                <td class="p-3">{{ ucfirst($order->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-5 text-center text-gray-500">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </section>
    </div>
</x-app-layout>
