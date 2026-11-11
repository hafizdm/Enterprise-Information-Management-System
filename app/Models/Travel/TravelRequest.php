<?php

namespace App\Models\Travel;

use App\Models\Project;
use App\Models\Spd;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends Model
{
    use SoftDeletes;

    protected $table = 'travel_requests';

    protected $fillable = [
        'request_number',
        'source_type',
        'spd_id',
        'project_id',
        'travel_category',
        'status',
        'request_date',
        'departure_date',
        'departure_time_window',
        'return_date',
        'return_time_window',
        'origin',
        'destination',
        'need_flight',
        'need_hotel',
        'need_local_transport',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'departure_date' => 'date',
            'return_date' => 'date',
            'need_flight' => 'boolean',
            'need_hotel' => 'boolean',
            'need_local_transport' => 'boolean',
        ];
    }

    public function spd(): BelongsTo
    {
        return $this->belongsTo(Spd::class, 'spd_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(TravelRequestPassenger::class, 'travel_request_id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(TravelInquiry::class, 'travel_request_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TravelBooking::class, 'travel_request_id');
    }

    public static function generateRequestNumber(): string
    {
        $yearMonth = date('Ym');
        $prefix = "TR-{$yearMonth}-";
        $lastNumber = static::where('request_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('request_number');

        if ($lastNumber) {
            $seq = (int) substr($lastNumber, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
