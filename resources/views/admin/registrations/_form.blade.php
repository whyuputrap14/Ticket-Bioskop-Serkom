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
        <label for="name" class="mb-1 block font-medium">Nama Penonton</label>
        <input id="name" name="name" type="text" value="{{ old('name', $registration->name ?? '') }}" maxlength="255" class="w-full rounded border p-2" required>
    </div>
    <div class="mb-4">
        <label for="email" class="mb-1 block font-medium">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $registration->email ?? '') }}" maxlength="255" class="w-full rounded border p-2" required>
    </div>
    <div class="mb-4">
        <label for="password" class="mb-1 block font-medium">Password {{ isset($registration) ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input id="password" name="password" type="password" autocomplete="new-password" {{ isset($registration) ? '' : 'required' }} class="w-full rounded border p-2">
    </div>
    <div class="mb-6">
        <label for="password_confirmation" class="mb-1 block font-medium">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" {{ isset($registration) ? '' : 'required' }} class="w-full rounded border p-2">
    </div>
    <div class="flex gap-3">
        <button type="submit" class="rounded bg-blue-600 px-6 py-2.5 font-semibold text-white hover:bg-blue-700">{{ $buttonLabel }}</button>
        <a href="{{ route('admin.registrations.index') }}" class="rounded bg-gray-200 px-6 py-2.5 font-semibold text-gray-800 hover:bg-gray-300">Batal</a>
    </div>
</form>
