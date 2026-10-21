<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pesanan</h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @include('admin.orders._form', [
            'action' => route('admin.orders.store'),
            'method' => 'POST',
            'buttonLabel' => 'Simpan Pesanan',
            'order' => null,
        ])
    </div>
</x-app-layout>
