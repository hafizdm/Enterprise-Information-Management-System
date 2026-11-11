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
        Schema::create('employee_business_cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->unique()
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('public_token', 64)->unique();

            $table->boolean('is_active')->default(false);
            $table->boolean('show_phone')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_photo')->default(false);
            $table->boolean('show_department')->default(false);
            $table->boolean('show_location')->default(false);

            $table->string('custom_title', 255)->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_business_cards');
    }
};
