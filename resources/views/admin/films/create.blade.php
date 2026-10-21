<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Film</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-200 p-6 rounded-lg shadow-sm border border-slate-300">
            @csrf
            @if($errors->any())
                <div class="mb-4 rounded border border-red-300 bg-red-100 p-3 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama Film</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Kategori</label>
                <select name="category" class="w-full border rounded p-2" required>
                    <option value="Aksi" @selected(old('category') === 'Aksi')>Aksi</option>
                    <option value="Drama" @selected(old('category') === 'Drama')>Drama</option>
                    <option value="Komedi" @selected(old('category') === 'Komedi')>Komedi</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price') }}" min="0" step="1" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Deskripsi</label>
                <textarea name="description" class="w-full border rounded p-2" rows="3" required>{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Gambar Film</label>
                <input type="file" name="image" class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded font-semibold">Simpan Data</button>
        </form>
    </div>
</x-app-layout>