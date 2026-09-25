<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions.
     */
    public function index()
    {
        $divisions = Division::latest()->get();

        return view('master.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new division.
     */
    public function create()
    {
        return view('master.divisions.create');
    }

    /**
     * Store a newly created division.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:divisions,code',
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        Division::create($validated);

        return redirect()
            ->route('divisions.index')
            ->with('success', 'Division berhasil ditambahkan.');
    }

    /**
     * Display the specified division.
     */
    public function show(Division $division)
    {
        return view('master.divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified division.
     */
    public function edit(Division $division)
    {
        return view('master.divisions.edit', compact('division'));
    }

    /**
     * Update the specified division.
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('divisions', 'code')->ignore($division->id),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $division->update($validated);

        return redirect()
            ->route('divisions.index')
            ->with('success', 'Division berhasil diperbarui.');
    }

    /**
     * Remove the specified division.
     */
    public function destroy(Division $division)
    {
        $division->delete();

        return redirect()
            ->route('divisions.index')
            ->with('success', 'Division berhasil dihapus.');
    }
}