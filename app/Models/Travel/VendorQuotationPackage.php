<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VendorQuotationPackage extends Model
{
    protected $table = 'vendor_quotation_packages';

    protected $fillable = [
        'quotation_number',
        'travel_inquiry_id',
        'vendor_id',
        'package_name',
        'total_estimated_cost',
        'valid_until',
        'status',
        'vendor_notes',
        'internal_notes',
        'selected_at',
        'selected_by',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
            'selected_at' => 'datetime',
            'total_estimated_cost' => 'decimal:2',
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(TravelInquiry::class, 'travel_inquiry_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function selectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(VendorQuotationService::class, 'vendor_quotation_package_id');
    }

    public function booking(): HasOne
    {
        return $this->hasOne(TravelBooking::class, 'vendor_quotation_package_id');
    }

    public static function generateQuotationNumber(): string
    {
        $yearMonth = date('Ym');
        $prefix = "QUO-{$yearMonth}-";
        $lastNumber = static::where('quotation_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('quotation_number');

        if ($lastNumber) {
            $seq = (int) substr($lastNumber, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
