<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelTicket extends Model
{
    protected $table = 'travel_tickets';

    protected $fillable = [
        'ticket_number',
        'travel_booking_id',
        'travel_booking_service_id',
        'ticket_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'uploaded_at',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TravelBooking::class, 'travel_booking_id');
    }

    public function bookingService(): BelongsTo
    {
        return $this->belongsTo(TravelBookingService::class, 'travel_booking_service_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
