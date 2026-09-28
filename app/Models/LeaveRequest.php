<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'first_date',
        'last_date',
        'total_days',
        'reason',
        'manager_id',
        'hr_approver_id',
        'status',
        'manager_approved_at',
        'hr_approved_at',
        'manager_rejection_reason',
        'hr_rejection_reason',
    ];

    protected $casts = [
        'first_date' => 'date',
        'last_date' => 'date',
        'manager_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function hrApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_approver_id');
    }
}