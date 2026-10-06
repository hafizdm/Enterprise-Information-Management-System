<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Employee
            'employee.view-own',
            'employee.update-own',
            'employee.view-any',
            'employee.create',
            'employee.update-any',
            'employee.delete',

            // User
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Role
            'role.view',
            'role.create',
            'role.update',
            'role.delete',

            // Permission
            'permission.view',
            'permission.manage',

            // SPD
            'spd.view-own',
            'spd.create',
            'spd.approve',
            'spd.view-any',
            'spd.delete',

            // SPD Report
            'spd-report.view-own',
            'spd-report.create',
            'spd-report.view-approval',
            'spd-report.approve',
            'spd-report.reject',
            'spd-report.view-any',

            // Cost Level
            'cost-level.view',
            'cost-level.create',
            'cost-level.update',
            'cost-level.delete',

            // Cash Advance
            'ca.view-own',
            'ca.create',
            'ca.approve',

            // RFP
            'rfp.view-own',
            'rfp.create',
            'rfp.approve',

            // Leave
            'leave.view-own',
            'leave.view-any',
            'leave.create',
            'leave.approve',

            // Reports
            'report.view',
            'report.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            'System Administrator',
            'Employee',
            'Employee Approval',
            'Finance',
            'HRD',
            'Document Control',
            'Board of Director',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Permissions
        |--------------------------------------------------------------------------
        */

        $rolePermissions = [
            'System Administrator' => $permissions,

            'Employee' => [
                'dashboard.view',

                'employee.view-own',
                'employee.update-own',

                'spd.view-own',

                'spd-report.view-own',
                'spd-report.create',

                'ca.view-own',
                'ca.create',

                'rfp.view-own',
                'rfp.create',

                'leave.view-own',
                'leave.create',
            ],

            'Employee Approval' => [
                'dashboard.view',

                'employee.view-any',

                'spd.view-own',
                'spd.approve',

                'spd-report.view-approval',
                'spd-report.approve',
                'spd-report.reject',

                'ca.approve',

                'rfp.approve',

                'leave.approve',
            ],

            'Finance' => [
                'dashboard.view',

                'employee.view-own',
                'employee.update-own',

                'spd.view-own',

                'ca.view-own',
                'ca.approve',

                'rfp.view-own',
                'rfp.approve',

                'report.view',
                'report.export',
            ],

            'HRD' => [
                'dashboard.view',

                'employee.view-any',
                'employee.create',
                'employee.update-any',
                'employee.delete',

                'spd.view-any',
                'spd.create',
                'spd.delete',

                'spd-report.view-any',

                'leave.view-any',

                'report.view',
                'report.export',
            ],

            'Document Control' => [
                'dashboard.view',

                'employee.view-any',

                'rfp.view-own',
                'rfp.approve',

                'report.view',
                'report.export',
            ],

            'Board of Director' => [
                'dashboard.view',

                'employee.view-any',

                'report.view',
                'report.export',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                $role->syncPermissions($permissionNames);
            }
        }
    }
}

