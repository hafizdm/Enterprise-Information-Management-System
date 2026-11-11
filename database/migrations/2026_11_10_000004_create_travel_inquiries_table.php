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
        Schema::create('travel_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number', 50)->unique();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->enum('status', ['sent', 'quoted', 'expired', 'cancelled'])->default('sent');
            $table->timestamp('deadline_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('travel_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_inquiries');
    }
};
