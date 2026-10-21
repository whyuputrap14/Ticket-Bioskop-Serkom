<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->latest()->paginate(15);

        return view('admin.registrations.index', compact('users'));
    }

    public function create()
    {
        return view('admin.registrations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        return redirect()->route('admin.registrations.index')->with('success', 'Data penonton berhasil ditambahkan.');
    }

    public function edit(User $registration)
    {
        abort_unless($registration->role === 'user', 404);

        return view('admin.registrations.edit', compact('registration'));
    }

    public function update(Request $request, User $registration)
    {
        abort_unless($registration->role === 'user', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($registration->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $registration->name = $validated['name'];
        $registration->email = $validated['email'];

        if (! empty($validated['password'])) {
            $registration->password = Hash::make($validated['password']);
        }

        $registration->save();

        return redirect()->route('admin.registrations.index')->with('success', 'Data penonton berhasil diperbarui.');
    }

    public function destroy(User $registration)
    {
        abort_unless($registration->role === 'user', 404);
        $registration->delete();

        return redirect()->route('admin.registrations.index')->with('success', 'Data penonton berhasil dihapus.');
    }
}
