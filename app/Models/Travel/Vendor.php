<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activeHistories(): HasMany
    {
        return $this->hasMany(VendorActiveHistory::class, 'vendor_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'vendor_id');
    }
}
