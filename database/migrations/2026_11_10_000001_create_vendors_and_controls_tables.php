<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 100)->nullable();
            $table->string('bank_account_holder', 150)->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('travel_vendor_controls', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->default(1)->primary();
            $table->foreignId('active_vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('vendor_active_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')
                ->constrained('vendors')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('effective_date');
            $table->date('deactivated_date')->nullable();
            $table->foreignId('changed_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->text('reason');
            $table->timestamps();

            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_active_histories');
        Schema::dropIfExists('travel_vendor_controls');
        Schema::dropIfExists('vendors');
    }
};
