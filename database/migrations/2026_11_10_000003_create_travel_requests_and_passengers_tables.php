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
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique();
            $table->enum('source_type', ['spd', 'project'])->default('spd');
            $table->foreignId('spd_id')->nullable()->unique()->constrained('spds')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->enum('travel_category', ['domestic', 'international'])->default('domestic');
            $table->enum('status', [
                'pending',
                'in_inquiry',
                'quoted',
                'approved',
                'booked',
                'ticket_issued',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->date('request_date');
            $table->date('departure_date');
            $table->string('departure_time_window')->nullable();
            $table->date('return_date')->nullable();
            $table->string('return_time_window')->nullable();
            $table->string('origin');
            $table->string('destination');
            $table->boolean('need_flight')->default(false);
            $table->boolean('need_hotel')->default(false);
            $table->boolean('need_local_transport')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'status']);
            $table->index('departure_date');
        });

        Schema::create('travel_request_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('passenger_name');
            $table->string('id_card_number', 50)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('specific_needs')->nullable();
            $table->timestamps();

            $table->index('passenger_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_request_passengers');
        Schema::dropIfExists('travel_requests');
    }
};
