<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jadwal Tayang</h2>
            <a href="{{ route('admin.showtimes.create') }}" class="rounded bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">+ Tambah Jadwal</a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
        @endif
        <div class="overflow-x-auto rounded-lg border border-slate-300 bg-slate-200 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-sm uppercase text-gray-600">
                    <tr>
                        <th class="p-4">Film</th>
                        <th class="p-4">Tanggal & Jam</th>
                        <th class="p-4">Studio</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($showtimes as $showtime)
                        <tr>
                            <td class="p-4 font-semibold">{{ $showtime->film->name }}</td>
                            <td class="p-4">{{ $showtime->starts_at->format('d-m-Y H:i') }}</td>
                            <td class="p-4">{{ $showtime->hall }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <a class="mr-3 font-semibold text-blue-600 hover:underline" href="{{ route('admin.showtimes.edit', $showtime) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('admin.showtimes.destroy', $showtime) }}" onsubmit="return confirm('Hapus jadwal tayang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-semibold text-red-600 hover:underline" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Belum ada jadwal tayang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $showtimes->links() }}</div>
    </div>
</x-app-layout>
