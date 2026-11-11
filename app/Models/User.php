<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'user_type',
        'employee_id',
        'vendor_id',
        'username',
        'email',
        'password',
        'must_change_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Travel\Vendor::class);
    }

    public function isVendor(): bool
    {
        return $this->user_type === 'vendor' || $this->vendor_id !== null;
    }

    public function isInternal(): bool
    {
        return $this->user_type === 'internal' && $this->vendor_id === null;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->employee?->email ?? $this->email ?? $this->vendor?->email;
    }
}