<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorQuotationService extends Model
{
    protected $table = 'vendor_quotation_services';

    protected $fillable = [
        'vendor_quotation_package_id',
        'service_type',
        'service_name',
        'service_date',
        'service_details',
        'estimated_cost',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'estimated_cost' => 'decimal:2',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(VendorQuotationPackage::class, 'vendor_quotation_package_id');
    }
}
