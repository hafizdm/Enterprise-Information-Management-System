<?php

namespace App\Models\Travel;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelRequestPassenger extends Model
{
    protected $table = 'travel_request_passengers';

    protected $fillable = [
        'travel_request_id',
        'employee_id',
        'passenger_name',
        'id_card_type',
        'id_card_number',
        'phone_number',
        'gender',
        'specific_needs',
    ];

    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
