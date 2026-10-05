<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpdReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'spd_id',
        'employee_id',

        // Actual Travel
        'date_departure',
        'date_return',
        'total_days',

        // Actual Expenses
        'meals_per_day',
        'allowance_per_day',
        'local_transport',
        'contingencies',

        // Financial Calculation
        'balance_received',
        'expense_balance',
        'expense_report_total',

        // Report Workflow
        'status_report',
        'settlement_status',

        // Manager Approval
        'manager_approved_at',
        'manager_rejection_reason',

        // Supporting Evidence
        'expense_evidence',
        'note',

        // Submission
        'submitted_at',
    ];

    protected $casts = [
        'date_departure' => 'date',
        'date_return' => 'date',

        'total_days' => 'integer',

        'meals_per_day' => 'decimal:2',
        'allowance_per_day' => 'decimal:2',
        'local_transport' => 'decimal:2',
        'contingencies' => 'decimal:2',

        'balance_received' => 'decimal:2',
        'expense_balance' => 'decimal:2',
        'expense_report_total' => 'decimal:2',

        'manager_approved_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * SPD associated with this report.
     */
    public function spd(): BelongsTo
    {
        return $this->belongsTo(Spd::class, 'spd_id');
    }

    /**
     * Employee who submits the report.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
