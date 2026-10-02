<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CostLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostLevelController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->can('cost-level.view'),
            403,
            'You are not authorized to view cost levels.'
        );

        $costLevels = CostLevel::orderBy('name')->paginate(10);

        return view('master.cost_levels.index', compact('costLevels'));
    }

    public function create()
    {
        abort_unless(
            auth()->user()->can('cost-level.create'),
            403,
            'You are not authorized to create cost levels.'
        );

        return view('master.cost_levels.create');
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->can('cost-level.create'),
            403,
            'You are not authorized to create cost levels.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:cost_levels,name',
            ],
            'meals_domestic' => [
                'required',
                'numeric',
                'min:0',
            ],
            'allowance_domestic' => [
                'required',
                'numeric',
                'min:0',
            ],
            'meals_international' => [
                'required',
                'numeric',
                'min:0',
            ],
            'allowance_international' => [
                'required',
                'numeric',
                'min:0',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        CostLevel::create($validated);

        return redirect()
            ->route('cost-levels.index')
            ->with(
                'success',
                'Cost Level berhasil dibuat.'
            );
    }

    public function show(CostLevel $costLevel)
    {
        abort_unless(
            auth()->user()->can('cost-level.view'),
            403,
            'You are not authorized to view this cost level.'
        );

        $costLevel->loadCount('employees');

        return view(
            'master.cost_levels.show',
            compact('costLevel')
        );
    }

    public function edit(CostLevel $costLevel)
    {
        abort_unless(
            auth()->user()->can('cost-level.update'),
            403,
            'You are not authorized to edit cost levels.'
        );

        return view(
            'master.cost_levels.edit',
            compact('costLevel')
        );
    }

    public function update(
        Request $request,
        CostLevel $costLevel
    ) {
        abort_unless(
            auth()->user()->can('cost-level.update'),
            403,
            'You are not authorized to update cost levels.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cost_levels', 'name')
                    ->ignore($costLevel->id),
            ],
            'meals_domestic' => [
                'required',
                'numeric',
                'min:0',
            ],
            'allowance_domestic' => [
                'required',
                'numeric',
                'min:0',
            ],
            'meals_international' => [
                'required',
                'numeric',
                'min:0',
            ],
            'allowance_international' => [
                'required',
                'numeric',
                'min:0',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $costLevel->update($validated);

        return redirect()
            ->route('cost-levels.index')
            ->with(
                'success',
                'Cost Level berhasil diperbarui.'
            );
    }

    public function destroy(CostLevel $costLevel)
    {
        abort_unless(
            auth()->user()->can('cost-level.delete'),
            403,
            'You are not authorized to delete cost levels.'
        );

        abort_unless(
            $costLevel->employees()->doesntExist(),
            422,
            'This Cost Level cannot be deleted because it is already assigned to an employee.'
        );

        $costLevel->delete();

        return redirect()
            ->route('cost-levels.index')
            ->with(
                'success',
                'Cost Level berhasil dihapus.'
            );
    }
}