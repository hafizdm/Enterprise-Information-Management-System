<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Employee;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->input('search');

        $users = User::with([
                'employee',
                'roles',
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search) {
                            $query->where('full_name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'search'
        ));
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        $employees = \App\Models\Employee::whereDoesntHave('user')
            ->orderBy('full_name')
            ->get();

        $roles = \Spatie\Permission\Models\Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact(
            'employees',
            'roles'
        ));
    }

    /**
 * Store a newly created user.
 */
    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $employee = Employee::findOrFail(
                $validated['employee_id']
            );

            if ($employee->user()->exists()) {
                abort(422, 'This employee already has a user account.');
            }

            $user = User::create([
                'employee_id' => $employee->id,
                'username' => $validated['username'],
                'password' => $validated['password'],
                'must_change_password' => true,
                'is_active' => $validated['is_active'],
            ]);

            $user->syncRoles($validated['roles']);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    /**
 * Display the specified user.
 */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        $user->load([
            'employee',
            'roles',
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
 * Show the form for editing the specified user.
 */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $user->load([
            'employee',
            'roles',
        ]);

        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact(
            'user',
            'roles'
        ));
    }

    /**
 * Update the specified user.
 */
    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')
                    ->ignore($user->id),
            ],

            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        DB::transaction(function () use ($validated, $user) {

            $data = [
                'username' => $validated['username'],
                'is_active' => $validated['is_active'],
            ];

            if (!empty($validated['password'])) {
                $data['password'] = $validated['password'];
                $data['must_change_password'] = true;
            }

            $user->update($data);

            $user->syncRoles($validated['roles']);
        });

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User account updated successfully.');
    }
}