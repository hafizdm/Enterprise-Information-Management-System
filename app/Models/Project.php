<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'location',
        'cost_center',
        'approval_employee_id',
    ];

    public function approvalEmployee()
    {
        return $this->belongsTo(Employee::class, 'approval_employee_id');
    }

    public function spds(): HasMany
    {
        return $this->hasMany(Spd::class);
    }
}