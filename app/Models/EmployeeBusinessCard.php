<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBusinessCard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_business_cards';

    /**
     * The attributes that are mass assignable.
     *
     * Note: 'public_token' is explicitly excluded from mass assignment
     * to protect against accidental token overwrite or rotation.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'is_active',
        'show_phone',
        'show_email',
        'show_photo',
        'show_department',
        'show_location',
        'custom_title',
        'scan_count',
        'last_scanned_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'show_photo' => 'boolean',
            'show_department' => 'boolean',
            'show_location' => 'boolean',
            'scan_count' => 'integer',
            'last_scanned_at' => 'datetime',
        ];
    }

    /**
     * Get the employee that owns the business card.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include active cards.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query by public token.
     */
    public function scopeByToken($query, string $token)
    {
        return $query->where('public_token', $token);
    }
}
