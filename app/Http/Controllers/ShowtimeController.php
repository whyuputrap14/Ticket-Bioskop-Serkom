<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class ShowtimeController extends Controller
{
    public function index()
    {
        $showtimes = Showtime::with('film')->orderBy('starts_at')->paginate(10);

        return view('admin.showtimes.index', compact('showtimes'));
    }

    public function create()
    {
        $films = Film::orderBy('name')->get();

        return view('admin.showtimes.create', compact('films'));
    }

    public function store(Request $request)
    {
        Showtime::create($this->validateShowtime($request));

        return redirect()->route('admin.showtimes.index')->with('success', 'Jadwal tayang berhasil ditambahkan.');
    }

    public function edit(Showtime $showtime)
    {
        $films = Film::orderBy('name')->get();

        return view('admin.showtimes.edit', compact('showtime', 'films'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $showtime->update($this->validateShowtime($request, $showtime));

        return redirect()->route('admin.showtimes.index')->with('success', 'Jadwal tayang berhasil diperbarui.');
    }

    public function destroy(Showtime $showtime)
    {
        $showtime->delete();

        return redirect()->route('admin.showtimes.index')->with('success', 'Jadwal tayang berhasil dihapus.');
    }

    private function validateShowtime(Request $request, ?Showtime $showtime = null): array
    {
        $validated = $request->validate([
            'film_id' => ['required', 'integer', 'exists:films,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'hall' => ['required', 'string', 'max:100'],
        ]);

        $startsAt = Carbon::parse($validated['starts_at'])->format('Y-m-d H:i:s');
        $duplicate = Showtime::query()
            ->where('film_id', $validated['film_id'])
            ->where('starts_at', $startsAt)
            ->where('hall', $validated['hall'])
            ->when($showtime, fn ($query) => $query->where('id', '!=', $showtime->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'hall' => 'Jadwal film di studio tersebut sudah terdaftar.',
            ]);
        }

        $validated['starts_at'] = $startsAt;

        return $validated;
    }
}
