<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $films = Film::latest()->paginate(10);
        return view('admin.films.index', compact('films'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.films.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Aksi,Drama,Komedi',
            'price'       => 'required|integer|min:0',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('films', 'public');
        }

        Film::create($validated);

        return redirect()->route('admin.films.index')->with('success', 'Data film berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film)
    {
        return view('admin.films.edit', compact('film'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Aksi,Drama,Komedi',
            'price'       => 'required|integer|min:0',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('films', 'public');

            if ($film->image && Storage::disk('public')->exists($film->image)) {
                Storage::disk('public')->delete($film->image);
            }
        }

        $film->update($validated);

        return redirect()->route('admin.films.index')->with('success', 'Data film berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        if ($film->image && Storage::disk('public')->exists($film->image)) {
            Storage::disk('public')->delete($film->image);
        }
        $film->delete();

        return redirect()->route('admin.films.index')->with('success', 'Data film berhasil dihapus!');
    }
}
