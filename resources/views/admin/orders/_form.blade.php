<form action="{{ $action }}" method="POST" class="bg-slate-200 p-6 rounded-lg shadow-sm border border-slate-300">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

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
        <label for="customer_name" class="block font-medium mb-1">Nama Pelanggan</label>
        <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name ?? '') }}" maxlength="255" class="w-full border rounded p-2" required>
    </div>

    <div class="mb-4">
        <label for="seat_number" class="block font-medium mb-1">Nomor Kursi</label>
        <input id="seat_number" type="number" name="seat_number" value="{{ old('seat_number', $order->seat_number ?? '') }}" min="1" step="1" class="w-full border rounded p-2" required>
    </div>

    <div class="mb-6">
        <label for="status" class="block font-medium mb-1">Status Pesanan</label>
        <select id="status" name="status" class="w-full border rounded p-2" required>
            <option value="pending" @selected(old('status', $order->status ?? 'pending') === 'pending')>Pending</option>
            <option value="completed" @selected(old('status', $order->status ?? 'pending') === 'completed')>Selesai / Lunas</option>
            <option value="cancelled" @selected(old('status', $order->status ?? 'pending') === 'cancelled')>Dibatalkan</option>
        </select>
    </div>

    <fieldset class="mb-6">
        <legend class="font-semibold mb-2">Film dan Jumlah Tiket</legend>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3 border">Film</th>
                        <th class="p-3 border">Jadwal</th>
                        <th class="p-3 border">Harga</th>
                        <th class="p-3 border">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($films as $index => $film)
                        <tr>
                            <td class="p-3 border">{{ $film->name }}</td>
                            <td class="p-3 border">
                                <select name="items[{{ $index }}][showtime_id]" class="w-48 rounded border p-2">
                                    <option value="">Pilih jadwal</option>
                                    @foreach($film->showtimes as $showtime)
                                        <option value="{{ $showtime->id }}" @selected((string) old('items.' . $index . '.showtime_id', ($selectedShowtimes ?? [])[$film->id] ?? '') === (string) $showtime->id)>
                                            {{ $showtime->starts_at->format('d-m-Y H:i') }} · {{ $showtime->hall }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-3 border">Rp {{ number_format($film->price) }}</td>
                            <td class="p-3 border">
                                <input type="hidden" name="items[{{ $index }}][film_id]" value="{{ $film->id }}">
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ old('items.' . $index . '.quantity', $selectedQuantities[$film->id] ?? 0) }}" min="0" step="1" class="w-24 border rounded p-2" aria-label="Jumlah tiket {{ $film->name }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-3 text-center text-gray-500">Belum ada film. Tambahkan film terlebih dahulu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </fieldset>

    <div class="flex gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded font-semibold">{{ $buttonLabel }}</button>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2.5 rounded font-semibold">Batal</a>
    </div>
</form>
