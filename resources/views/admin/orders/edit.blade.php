<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Pesanan #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @include('admin.orders._form', [
            'action' => route('admin.orders.update', $order),
            'method' => 'PUT',
            'buttonLabel' => 'Simpan Perubahan',
        ])
    </div>
</x-app-layout>
