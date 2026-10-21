<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Data Film</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.films.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 inline-block font-semibold">+ Tambah Film</a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full bg-slate-200 border border-slate-300 mt-4 shadow-sm rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 border-b text-gray-600 text-sm">
                    <th class="p-3">Gambar</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Harga</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($films as $film)
                <tr class="border-b text-center text-sm">
                    <td class="p-3">
                        @if($film->image)
                            <img src="{{ asset('storage/' . $film->image) }}" class="w-16 h-16 object-cover mx-auto rounded">
                        @else
                            <span class="text-gray-400 text-xs">No Image</span>
                        @endif
                    </td>
                    <td class="p-3 font-semibold">{{ $film->name }}</td>
                    <td class="p-3">{{ $film->category }}</td>
                    <td class="p-3 font-bold text-green-600">Rp {{ number_format($film->price) }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.films.edit', $film->id) }}" class="text-blue-600 hover:underline mr-3 font-semibold">Edit</a>
                        <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $films->links() }}</div>
    </div>
</x-app-layout>