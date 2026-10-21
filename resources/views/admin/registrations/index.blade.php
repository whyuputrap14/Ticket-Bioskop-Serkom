<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrasi User</h2></x-slot>
    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
        @endif
        <div class="mb-4 flex items-center justify-between">
            <p class="text-gray-600">Daftar penonton: {{ $users->total() }}</p>
            <a href="{{ route('admin.registrations.create') }}" class="rounded bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">+ Tambah Penonton</a>
        </div>
        <div class="overflow-x-auto rounded-lg border border-slate-300 bg-slate-200 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-sm uppercase text-gray-600">
                    <tr><th class="p-4">Nama</th><th class="p-4">Email</th><th class="p-4">Tanggal Registrasi</th><th class="p-4">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr>
                            <td class="p-4 font-semibold">{{ $user->name }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">{{ $user->created_at->format('d-m-Y H:i') }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <a href="{{ route('admin.registrations.edit', $user) }}" class="mr-3 font-semibold text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.registrations.destroy', $user) }}" class="inline" onsubmit="return confirm('Hapus data penonton {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Belum ada penonton terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
