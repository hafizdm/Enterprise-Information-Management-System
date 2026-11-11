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
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 50)->unique();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->foreignId('vendor_quotation_package_id')->constrained('vendor_quotation_packages')->restrictOnDelete();
            $table->enum('status', [
                'confirmed',
                'ticket_issued',
                'verified',
                'completed',
                'cancelled',
            ])->default('confirmed');
            $table->decimal('total_actual_amount', 15, 2)->default(0);
            $table->timestamp('confirmed_at')->useCurrent();
            $table->foreignId('confirmed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('travel_request_id');
        });

        Schema::create('travel_booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_booking_id')->constrained('travel_bookings')->cascadeOnDelete();
            $table->foreignId('vendor_quotation_service_id')->nullable()->constrained('vendor_quotation_services')->nullOnDelete();
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
            $table->string('booking_reference')->nullable(); // PNR code or confirmation number
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->enum('status', ['booked', 'ticketed', 'cancelled'])->default('booked');
            $table->timestamps();

            $table->index(['travel_booking_id', 'status']);
        });

        Schema::create('travel_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 100);
            $table->foreignId('travel_booking_id')->constrained('travel_bookings')->cascadeOnDelete();
            $table->foreignId('travel_booking_service_id')->nullable()->constrained('travel_booking_services')->nullOnDelete();
            $table->enum('ticket_type', [
                'flight_eticket',
                'hotel_voucher',
                'train_ticket',
                'other',
            ])->default('flight_eticket');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['travel_booking_id', 'is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_tickets');
        Schema::dropIfExists('travel_booking_services');
        Schema::dropIfExists('travel_bookings');
    }
};
