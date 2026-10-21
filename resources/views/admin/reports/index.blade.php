<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Film per Periode</h2></x-slot>
    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm">
            <div>
                <label for="start_date" class="mb-1 block text-sm font-medium">Dari tanggal</label>
                <input id="start_date" name="start_date" type="date" value="{{ $startDate }}" class="rounded border p-2" required>
            </div>
            <div>
                <label for="end_date" class="mb-1 block text-sm font-medium">Sampai tanggal</label>
                <input id="end_date" name="end_date" type="date" value="{{ $endDate }}" class="rounded border p-2" required>
            </div>
            <button type="submit" class="rounded bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">Tampilkan Laporan</button>
        </form>

        @if($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-100 p-3 text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm"><p class="text-sm text-gray-500">Pesanan pada periode</p><p class="mt-1 text-2xl font-bold">{{ $summary['orders'] }}</p></div>
            <div class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm"><p class="text-sm text-gray-500">Nilai tiket aktif</p><p class="mt-1 text-2xl font-bold text-green-700">Rp {{ number_format($summary['revenue']) }}</p></div>
            <div class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm"><p class="text-sm text-gray-500">Selesai / Lunas</p><p class="mt-1 text-2xl font-bold">{{ $summary['completed'] }}</p></div>
            <div class="rounded-lg border border-slate-300 bg-slate-200 p-5 shadow-sm"><p class="text-sm text-gray-500">Menunggu pembayaran</p><p class="mt-1 text-2xl font-bold">{{ $summary['pending'] }}</p></div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-300 bg-slate-200 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-sm uppercase text-gray-600">
                    <tr><th class="p-4">Tanggal & Jam</th><th class="p-4">Film</th><th class="p-4">Studio</th><th class="p-4">Tiket Terjual</th><th class="p-4">Nilai Pesanan</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($showtimes as $showtime)
                        <tr>
                            <td class="p-4">{{ $showtime->starts_at->format('d-m-Y H:i') }}</td>
                            <td class="p-4 font-semibold">{{ $showtime->film->name }}</td>
                            <td class="p-4">{{ $showtime->hall }}</td>
                            <td class="p-4">{{ $showtime->tickets_sold }}</td>
                            <td class="p-4 font-semibold">Rp {{ number_format($showtime->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">Tidak ada jadwal film pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $showtimes->links() }}</div>
        <p class="mt-3 text-xs text-gray-500">Nilai laporan menghitung pesanan pending dan selesai; pesanan yang dibatalkan tidak dihitung.</p>
    </div>
</x-app-layout>
