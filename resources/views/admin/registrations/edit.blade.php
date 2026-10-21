<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Penonton</h2></x-slot>
    <div class="py-10 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @include('admin.registrations._form', [
            'action' => route('admin.registrations.update', $registration),
            'method' => 'PUT',
            'buttonLabel' => 'Simpan Perubahan',
        ])
    </div>
</x-app-layout>
