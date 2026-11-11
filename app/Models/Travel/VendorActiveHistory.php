<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorActiveHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'effective_date',
        'deactivated_date',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'deactivated_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
