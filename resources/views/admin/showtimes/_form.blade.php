<form method="POST" action="{{ $action }}" class="rounded-lg border border-slate-300 bg-slate-200 p-6 shadow-sm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="mb-4 rounded border border-red-300 bg-red-100 p-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4">
        <label for="film_id" class="mb-1 block font-medium">Film</label>
        <select id="film_id" name="film_id" class="w-full rounded border p-2" required>
            <option value="">Pilih film</option>
            @foreach($films as $film)
                <option value="{{ $film->id }}" @selected((string) old('film_id', $showtime->film_id ?? '') === (string) $film->id)>{{ $film->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label for="starts_at" class="mb-1 block font-medium">Tanggal dan Jam Tayang</label>
        <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', isset($showtime) ? $showtime->starts_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded border p-2" required>
    </div>
    <div class="mb-6">
        <label for="hall" class="mb-1 block font-medium">Studio</label>
        <input id="hall" name="hall" type="text" value="{{ old('hall', $showtime->hall ?? '') }}" maxlength="100" class="w-full rounded border p-2" required>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="rounded bg-blue-600 px-6 py-2.5 font-semibold text-white hover:bg-blue-700">{{ $buttonLabel }}</button>
        <a href="{{ route('admin.showtimes.index') }}" class="rounded bg-gray-200 px-6 py-2.5 font-semibold text-gray-800 hover:bg-gray-300">Batal</a>
    </div>
</form>
