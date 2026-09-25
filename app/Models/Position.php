<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    protected $fillable = [
        'division_id',
        'name',
        'description',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}