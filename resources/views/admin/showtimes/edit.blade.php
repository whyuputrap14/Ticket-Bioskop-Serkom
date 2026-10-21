<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Jadwal Tayang</h2></x-slot>
    <div class="py-10 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @include('admin.showtimes._form', [
            'action' => route('admin.showtimes.update', $showtime),
            'method' => 'PUT',
            'buttonLabel' => 'Simpan Perubahan',
        ])
    </div>
</x-app-layout>
