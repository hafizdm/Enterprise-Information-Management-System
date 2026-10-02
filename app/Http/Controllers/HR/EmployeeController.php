<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\CostLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\LeaveRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
   public function index(Request $request)
    {
        Gate::authorize('viewAny', Employee::class);

        $user = $request->user();

        $query = Employee::with([
            'division',
            'position',
            'project',
            'manager',
            'user',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Employee Scope
        |--------------------------------------------------------------------------
        |
        | HRD / Admin yang memiliki employee.view-any
        | dapat melihat seluruh employee.
        |
        | Employee biasa hanya dapat melihat data dirinya sendiri.
        |
        */

        if ($user->can('employee.view-any')) {
            $search = $request->input('search');

            $query->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nik', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        } else {
            $query->where('id', $user->employee_id);
            $search = null;
        }

        $employees = $query
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        return view('hr.employees.index', compact(
            'employees',
            'search'
        ));
    }

  public function create()
    {
        Gate::authorize('create', Employee::class);

        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $costLevels = CostLevel::where('is_active', true)
            ->orderBy('name')
            ->get();

        $managers = Employee::orderBy('full_name')->get();

        return view('hr.employees.create', compact(
            'divisions',
            'positions',
            'projects',
            'costLevels',
            'managers'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Employee::class);

        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'max:50',
                'unique:employees,nik',
            ],

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:255',
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'religion' => [
                'required',
                Rule::in([
                    'Islam',
                    'Hindu',
                    'Buddha',
                    'Kristen',
                    'Katolik',
                    'Kong Hu Cu',
                ]),
            ],

            'gender' => [
                'required',
                Rule::in([
                    'Male',
                    'Female',
                ]),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:employees,email',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'npwp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'bpjs_health' => [
                'nullable',
                'string',
                'max:50',
            ],

            'bpjs_employment' => [
                'nullable',
                'string',
                'max:50',
            ],

            'division_id' => [
                'required',
                'exists:divisions,id',
            ],

            'position_id' => [
                'required',
                'exists:positions,id',
            ],

            'project_id' => [
                'nullable',
                'exists:projects,id',
            ],

            'cost_level_id' => [
                'required',
                'exists:cost_levels,id',
            ],

            'employee_status' => [
                'required',
                Rule::in([
                    'Contract',
                    'Permanent',
                ]),
            ],

            'contract_start_date' => [
                'nullable',
                'date',
            ],

            'contract_end_date' => [
                'nullable',
                'date',
                'after_or_equal:contract_start_date',
            ],

            'report_to' => [
                'nullable',
                'exists:employees,id',
                'not_in:' . ($request->input('id') ?? ''),
            ],

            'spd_limit' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_annual_leave' => [
                'required',
                'numeric',
                'min:0',
                'max:366',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'create_account' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Additional Contract Validation
        |--------------------------------------------------------------------------
        */

        if ($validated['employee_status'] === 'Contract') {
            $request->validate([
                'contract_start_date' => [
                    'required',
                    'date',
                ],
                'contract_end_date' => [
                    'required',
                    'date',
                    'after_or_equal:contract_start_date',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Employee + Optional User Account
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($request, $validated) {

            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')
                    ->store('employees/photos', 'public');
            }

            $employee = Employee::create([
                'nik' => $validated['nik'],
                'full_name' => $validated['full_name'],
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'religion' => $validated['religion'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'bpjs_health' => $validated['bpjs_health'] ?? null,
                'bpjs_employment' => $validated['bpjs_employment'] ?? null,
                'division_id' => $validated['division_id'],
                'position_id' => $validated['position_id'],
                'project_id' => $validated['project_id'] ?? null,
                'cost_level_id' => $validated['cost_level_id'],
                'employee_status' => $validated['employee_status'],
                'contract_start_date' => $validated['contract_start_date'] ?? null,
                'contract_end_date' => $validated['contract_end_date'] ?? null,
                'report_to' => $validated['report_to'] ?? null,
                'spd_limit' => $validated['spd_limit'],
                'total_annual_leave' => $validated['total_annual_leave'],
                'photo' => $photoPath,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Login Account
            |--------------------------------------------------------------------------
            */

            if ($request->boolean('create_account')) {

                $user = \App\Models\User::create([
                    'employee_id' => $employee->id,
                    'username' => $employee->nik,
                    'password' => '12345678',
                    'must_change_password' => true,
                    'is_active' => true,
                ]);

                $user->assignRole('Employee');
            }
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        Gate::authorize('view', $employee);

        $employee->load([
            'division',
            'position',
            'project',
            'costLevel',
            'manager',
            'user.roles',
        ]);

        $activeAnnualLeave = LeaveRequest::where(
            'employee_id',
            $employee->id
        )
            ->where('leave_type', 'annual')
            ->whereIn('status', ['pending_manager', 'approved'])
            ->sum('total_days');

        $remainingAnnualLeave =
            $employee->total_annual_leave - $activeAnnualLeave;

        return view(
            'hr.employees.show',
            compact(
                'employee',
                'remainingAnnualLeave'
            )
        );
    }

  public function edit(Employee $employee)
    {
        Gate::authorize('update', $employee);

        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $costLevels = CostLevel::where('is_active', true)
        ->orderBy('name')
        ->get();

        $managers = Employee::where('id', '!=', $employee->id)
            ->orderBy('full_name')
            ->get();

        $activeAnnualLeave = LeaveRequest::where(
            'employee_id',
            $employee->id
        )
            ->where('leave_type', 'annual')
            ->whereIn('status', ['pending_manager', 'approved'])
            ->sum('total_days');

        $remainingAnnualLeave =
            $employee->total_annual_leave - $activeAnnualLeave;

        return view('hr.employees.edit', compact(
            'employee',
            'divisions',
            'positions',
            'projects',
            'costLevels',
            'managers',
            'activeAnnualLeave',
            'remainingAnnualLeave'
        ));
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'nik')->ignore($employee->id),
            ],

            'full_name' => ['required', 'string', 'max:255'],

            'birth_place' => [
                'nullable',
                'string',
                'max:255',
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'religion' => [
                'required',
                Rule::in([
                    'Islam',
                    'Hindu',
                    'Buddha',
                    'Kristen',
                    'Katolik',
                    'Kong Hu Cu',
                ]),
            ],

            'gender' => [
                'required',
                Rule::in([
                    'Male',
                    'Female',
                ]),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id),
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'npwp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'bpjs_health' => [
                'nullable',
                'string',
                'max:50',
            ],

            'bpjs_employment' => [
                'nullable',
                'string',
                'max:50',
            ],

            'division_id' => [
                'required',
                'exists:divisions,id',
            ],

            'position_id' => [
                'required',
                'exists:positions,id',
            ],

            'project_id' => [
                'nullable',
                'exists:projects,id',
            ],

            'cost_level_id' => [
                'required',
                'exists:cost_levels,id',
            ],

            'employee_status' => [
                'required',
                Rule::in([
                    'Contract',
                    'Permanent',
                ]),
            ],

            'contract_start_date' => [
                'nullable',
                'date',
            ],

            'contract_end_date' => [
                'nullable',
                'date',
                'after_or_equal:contract_start_date',
            ],

            'report_to' => [
                'nullable',
                'exists:employees,id',
                Rule::notIn([$employee->id]),
            ],

            'spd_limit' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_annual_leave' => [
                'required',
                'numeric',
                'min:0',
                'max:366',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        if ($validated['employee_status'] === 'Contract') {
            $request->validate([
                'contract_start_date' => [
                    'required',
                    'date',
                ],

                'contract_end_date' => [
                    'required',
                    'date',
                    'after_or_equal:contract_start_date',
                ],
            ]);
        }

        if ($validated['employee_status'] === 'Permanent') {
            $validated['contract_start_date'] = null;
            $validated['contract_end_date'] = null;
        }

        DB::transaction(function () use (
            $request,
            $validated,
            $employee
        ) {
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request
                    ->file('photo')
                    ->store('employees/photos', 'public');
            }

            $employee->update([
                'nik' => $validated['nik'],
                'full_name' => $validated['full_name'],
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'religion' => $validated['religion'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'bpjs_health' => $validated['bpjs_health'] ?? null,
                'bpjs_employment' => $validated['bpjs_employment'] ?? null,
                'division_id' => $validated['division_id'],
                'position_id' => $validated['position_id'],
                'project_id' => $validated['project_id'] ?? null,
                'cost_level_id' => $validated['cost_level_id'],
                'employee_status' => $validated['employee_status'],
                'contract_start_date' => $validated['contract_start_date'] ?? null,
                'contract_end_date' => $validated['contract_end_date'] ?? null,
                'report_to' => $validated['report_to'] ?? null,
                'spd_limit' => $validated['spd_limit'],
                'total_annual_leave' => $validated['total_annual_leave'],
                'photo' => $validated['photo'] ?? $employee->photo,
            ]);
        });

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee berhasil diperbarui.');
    }
}