<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'meals_domestic',
        'allowance_domestic',
        'meals_international',
        'allowance_international',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meals_domestic' => 'decimal:2',
            'allowance_domestic' => 'decimal:2',
            'meals_international' => 'decimal:2',
            'allowance_international' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}