<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelBookingService extends Model
{
    protected $table = 'travel_booking_services';

    protected $fillable = [
        'travel_booking_id',
        'vendor_quotation_service_id',
        'service_type',
        'service_name',
        'booking_reference',
        'actual_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'actual_cost' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TravelBooking::class, 'travel_booking_id');
    }

    public function quotationService(): BelongsTo
    {
        return $this->belongsTo(VendorQuotationService::class, 'vendor_quotation_service_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TravelTicket::class, 'travel_booking_service_id');
    }
}
