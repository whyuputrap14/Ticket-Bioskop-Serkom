<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pesanan Masuk') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm transition">
                    + Tambah Pesanan
                </a>
                <a href="{{ route('admin.films.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-sm transition">
                    Kelola Film
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <section class="mb-8">
                <h3 class="mb-4 text-lg font-bold text-gray-800">Menu Dashboard Admin</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('admin.showtimes.index') }}" class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm transition hover:border-blue-400 hover:shadow-md">
                        <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xl text-blue-700" aria-hidden="true">1</span>
                        <h4 class="font-bold text-gray-800">Jadwal Tayang</h4>
                        <p class="mt-1 text-sm text-gray-500">Tambah, ubah, hapus, dan lihat jadwal film.</p>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm transition hover:border-green-400 hover:shadow-md">
                        <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-xl text-green-700" aria-hidden="true">2</span>
                        <h4 class="font-bold text-gray-800">Pemesanan Tiket</h4>
                        <p class="mt-1 text-sm text-gray-500">Kelola pesanan dan status pembayaran.</p>
                    </a>
                    <a href="{{ route('admin.registrations.index') }}" class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm transition hover:border-purple-400 hover:shadow-md">
                        <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-xl text-purple-700" aria-hidden="true">3</span>
                        <h4 class="font-bold text-gray-800">Registrasi</h4>
                        <p class="mt-1 text-sm text-gray-500">Kelola data akun penonton.</p>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm transition hover:border-amber-400 hover:shadow-md">
                        <span class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-700" aria-hidden="true">4</span>
                        <h4 class="font-bold text-gray-800">Laporan</h4>
                        <p class="mt-1 text-sm text-gray-500">Lihat laporan film dan tiket per periode.</p>
                    </a>
                </div>
            </section>

            <div class="bg-slate-200 overflow-hidden shadow-sm sm:rounded-lg border border-slate-300">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="p-4 border-b">No. Pesanan</th>
                                <th class="p-4 border-b">Nama Pelanggan</th>
                                <th class="p-4 border-b">Kursi</th>
                                <th class="p-4 border-b">Film</th>
                                <th class="p-4 border-b">Total</th>
                                <th class="p-4 border-b">Status</th>
                                <th class="p-4 border-b">Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-bold text-gray-700">#{{ $order->id }}</td>
                                    <td class="p-4 font-medium">{{ $order->customer_name }}</td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full text-xs">
                                            Kursi {{ $order->seat_number }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                                            @foreach($order->orderDetails as $detail)
                                                <li>
                                                    <strong>{{ $detail->film->name ?? 'Film' }}</strong> 
                                                    x{{ $detail->quantity }} 
                                                    <span class="text-xs text-gray-400">(Rp {{ number_format($detail->price * $detail->quantity) }})</span>
                                                    @if($detail->showtime)
                                                        <span class="block text-xs text-gray-500">{{ $detail->showtime->starts_at->format('d-m-Y H:i') }} · {{ $detail->showtime->hall }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-4 font-bold text-green-600">
                                        Rp {{ number_format($order->total_price) }}
                                    </td>
                                    <td class="p-4">
                                        @if(strtolower($order->status) == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded">PENDING</span>
                                        @elseif(strtolower($order->status) == 'completed' || strtolower($order->status) == 'selesai')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded">SELESAI</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded">{{ strtoupper($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1.5 bg-slate-100 shadow-sm font-semibold">
                                                <option value="pending" {{ strtolower($order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="completed" {{ strtolower($order->status) == 'completed' || strtolower($order->status) == 'selesai' ? 'selected' : '' }}>Selesai / Lunas</option>
                                                <option value="cancelled" {{ strtolower($order->status) == 'cancelled' || strtolower($order->status) == 'batal' ? 'selected' : '' }}>Batalkan</option>
                                            </select>  
                                        </form>
                                    </td>
                                    <td class="p-4 text-center whitespace-nowrap">
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="text-blue-600 hover:underline font-semibold mr-3">Edit</a>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pesanan #{{ $order->id }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $orders->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
