<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function view(User $user, Employee $employee): bool
    {
        if ($user->can('employee.view-any')) {
            return true;
        }

        return $user->can('employee.view-own')
            && $user->employee?->id === $employee->id;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('employee.view-any')
            || $user->can('employee.view-own');
    }

    public function create(User $user): bool
    {
        return $user->can('employee.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        if ($user->can('employee.update-any')) {
            return true;
        }

        return $user->can('employee.update-own')
            && $user->employee?->id === $employee->id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('employee.delete');
    }
}