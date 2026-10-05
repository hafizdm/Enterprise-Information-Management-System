<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spd extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'project_id',
        'manager_id',
        'approval_document_id',
        'travel_type',
        'from',
        'destination',
        'date_departure',
        'date_return',
        'total_days',
        'meals_per_day',
        'allowance_per_day',
        'local_transport',
        'contingencies',
        'balance_received',
        'transportation',
        'advance_payment',
        'note',
        'purpose',
        'status',
        'manager_approved_at',
        'manager_rejection_reason',
        'document_approved_at',
        'document_rejection_reason',
        'created_by',

        // SPD Document Numbering
        'spd_number',
        'document_year',
        'document_sequence',
    ];

    protected function casts(): array
    {
        return [
            'date_departure' => 'date',
            'date_return' => 'date',
            'meals_per_day' => 'decimal:2',
            'allowance_per_day' => 'decimal:2',
            'local_transport' => 'decimal:2',
            'contingencies' => 'decimal:2',
            'balance_received' => 'decimal:2',
            'advance_payment' => 'boolean',
            'manager_approved_at' => 'datetime',
            'document_approved_at' => 'datetime',

            // SPD Document Numbering
            'document_year' => 'integer',
            'document_sequence' => 'integer',
        ];
    }

    /**
     * Employee who will travel.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Project related to this SPD.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Employee's manager.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Project Approval Document employee.
     */
    public function approvalDocument(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approval_document_id');
    }

    /**
     * User who created the SPD.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * SPD Report associated with this SPD.
     */
    public function report(): HasOne
    {
        return $this->hasOne(SpdReport::class);
    }
}
