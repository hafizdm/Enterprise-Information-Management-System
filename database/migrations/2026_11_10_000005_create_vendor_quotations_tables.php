<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendor_quotation_packages', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 50)->unique();
            $table->foreignId('travel_inquiry_id')->constrained('travel_inquiries')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->string('package_name');
            $table->decimal('total_estimated_cost', 15, 2)->default(0);
            $table->timestamp('valid_until');
            $table->enum('status', [
                'submitted',
                'forwarded_to_site',
                'selected',
                'rejected',
                'expired',
            ])->default('submitted');
            $table->text('vendor_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('selected_at')->nullable();
            $table->foreignId('selected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['travel_inquiry_id', 'status']);
            $table->index('vendor_id');
        });

        Schema::create('vendor_quotation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_quotation_package_id')->constrained('vendor_quotation_packages')->cascadeOnDelete();
            $table->enum('service_type', [
                'flight',
                'hotel',
                'train',
                'bus',
                'car_rental',
                'ferry',
                'other',
            ])->default('flight');
            $table->string('service_name');
            $table->date('service_date');
            $table->text('service_details')->nullable();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['vendor_quotation_package_id', 'service_type'], 'vq_services_package_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_quotation_services');
        Schema::dropIfExists('vendor_quotation_packages');
    }
};
