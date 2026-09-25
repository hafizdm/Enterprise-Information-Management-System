<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('division')
            ->latest()
            ->paginate(5);

        return view('master.positions.index', compact('positions'));
    }

    public function create()
    {
        $divisions = Division::orderBy('name')->get();

        return view('master.positions.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Position::create($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Position berhasil ditambahkan.');
    }

    public function show(Position $position)
    {
        $position->load('division');

        return view('master.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $divisions = Division::orderBy('name')->get();

        return view('master.positions.edit', compact('position', 'divisions'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $position->update($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Position berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()
            ->route('positions.index')
            ->with('success', 'Position berhasil dihapus.');
    }
}