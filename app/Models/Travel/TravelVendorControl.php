<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelVendorControl extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'active_vendor_id',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function activeVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'active_vendor_id');
    }
}
