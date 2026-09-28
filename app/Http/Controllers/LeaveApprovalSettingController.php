<?php

namespace App\Http\Controllers;

use App\Models\LeaveApprovalSetting;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveApprovalSettingController extends Controller
{
    public function edit()
    {
        $setting = LeaveApprovalSetting::first();

        $users = User::with('employee')
        ->orderBy('username')
        ->get();

        return view('leave_approval_settings.edit', compact(
            'setting',
            'users'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hr_approver_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        LeaveApprovalSetting::updateOrCreate(
            ['id' => 1],
            [
                'hr_approver_id' => $validated['hr_approver_id'],
            ]
        );

        return redirect()
            ->route('leave-approval-settings.edit')
            ->with('success', 'Leave Approval Settings updated successfully.');
    }
}