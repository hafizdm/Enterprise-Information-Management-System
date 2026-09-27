<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'address',
        'religion',
        'gender',
        'email',
        'phone_number',
        'npwp',
        'bpjs_health',
        'bpjs_employment',
        'division_id',
        'position_id',
        'project_id',
        'employee_status',
        'contract_start_date',
        'contract_end_date',
        'report_to',
        'spd_limit',
        'total_annual_leave',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'spd_limit' => 'decimal:2',
        'total_annual_leave' => 'decimal:2',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'report_to');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'report_to');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}