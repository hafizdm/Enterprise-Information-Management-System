<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelInquiry extends Model
{
    protected $table = 'travel_inquiries';

    protected $fillable = [
        'inquiry_number',
        'travel_request_id',
        'vendor_id',
        'status',
        'deadline_at',
        'notes',
        'sent_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'deadline_at' => 'datetime',
        ];
    }

    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(VendorQuotationPackage::class, 'travel_inquiry_id');
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    public static function generateInquiryNumber(): string
    {
        $yearMonth = date('Ym');
        $prefix = "INQ-{$yearMonth}-";
        $lastNumber = static::where('inquiry_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('inquiry_number');

        if ($lastNumber) {
            $seq = (int) substr($lastNumber, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
