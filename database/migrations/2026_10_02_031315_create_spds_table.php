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
        Schema::create('spds', function (Blueprint $table) {
            $table->id();

            // Employee who will travel.
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Project related to the business trip.
            // Cost Center will be retrieved from the Project.
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Employee's direct manager.
            $table->foreignId('manager_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Approval Document employee assigned by the Project.
            $table->foreignId('approval_document_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Domestic or international travel.
            $table->enum('travel_type', [
                'domestic',
                'international',
            ]);

            $table->string('from');

            $table->string('destination');

            $table->date('date_departure');

            $table->date('date_return');

            // Calculated automatically from departure and return dates.
            $table->unsignedInteger('total_days');

            // Daily amounts.
            $table->decimal('meals_per_day', 15, 2)
                ->default(0);

            $table->decimal('allowance_per_day', 15, 2)
                ->default(0);

            // Optional costs.
            $table->decimal('local_transport', 15, 2)
                ->default(0);

            $table->decimal('contingencies', 15, 2)
                ->default(0);

            // Calculated automatically by the system.
            $table->decimal('balance_received', 15, 2)
                ->default(0);

            $table->enum('transportation', [
                'car',
                'plane',
                'ship',
                'other',
            ]);

            $table->boolean('advance_payment')
                ->default(false);

            $table->text('note')
                ->nullable();

            $table->text('purpose');

            // Approval workflow status.
            $table->enum('status', [
                'pending_manager',
                'pending_document',
                'approved',
                'rejected',
            ])->default('pending_manager');

            // Manager approval information.
            $table->timestamp('manager_approved_at')
                ->nullable();

            $table->text('manager_rejection_reason')
                ->nullable();

            // Project Approval Document information.
            $table->timestamp('document_approved_at')
                ->nullable();

            $table->text('document_rejection_reason')
                ->nullable();

            // HRD/User who created the SPD.
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spds');
    }
};