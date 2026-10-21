<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Jadwal Tayang</h2></x-slot>
    <div class="py-10 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @include('admin.showtimes._form', [
            'action' => route('admin.showtimes.store'),
            'method' => 'POST',
            'buttonLabel' => 'Simpan Jadwal',
            'showtime' => null,
        ])
    </div>
</x-app-layout>
