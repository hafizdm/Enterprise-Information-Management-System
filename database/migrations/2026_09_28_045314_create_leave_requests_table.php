<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('leave_type');

            $table->date('first_date');
            $table->date('last_date');

            $table->unsignedInteger('total_days');

            $table->text('reason')->nullable();

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('hr_approver_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('status')->default('pending_manager');

            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamp('hr_approved_at')->nullable();

            $table->text('manager_rejection_reason')->nullable();
            $table->text('hr_rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};