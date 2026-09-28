<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'location',
        'approval_employee_id',
    ];

    public function approvalEmployee()
    {
        return $this->belongsTo(Employee::class, 'approval_employee_id');
    }
}