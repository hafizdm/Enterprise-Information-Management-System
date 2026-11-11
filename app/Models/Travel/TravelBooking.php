<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelBooking extends Model
{
    protected $table = 'travel_bookings';

    protected $fillable = [
        'booking_number',
        'travel_request_id',
        'vendor_id',
        'vendor_quotation_package_id',
        'status',
        'total_actual_amount',
        'confirmed_at',
        'confirmed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'total_actual_amount' => 'decimal:2',
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

    public function quotationPackage(): BelongsTo
    {
        return $this->belongsTo(VendorQuotationPackage::class, 'vendor_quotation_package_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(TravelBookingService::class, 'travel_booking_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TravelTicket::class, 'travel_booking_id');
    }

    public static function generateBookingNumber(): string
    {
        $yearMonth = date('Ym');
        $prefix = "BKG-{$yearMonth}-";
        $lastNumber = static::where('booking_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('booking_number');

        if ($lastNumber) {
            $seq = (int) substr($lastNumber, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
