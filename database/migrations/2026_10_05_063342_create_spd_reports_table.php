<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spd_reports', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            // One SPD can only have one report.
            $table->foreignId('spd_id')
                ->constrained('spds')
                ->cascadeOnDelete()
                ->unique();

            // Employee who submits the report.
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Actual Travel Period
            |--------------------------------------------------------------------------
            */

            $table->date('date_departure');
            $table->date('date_return');
            $table->unsignedInteger('total_days');

            /*
            |--------------------------------------------------------------------------
            | Actual Expenses
            |--------------------------------------------------------------------------
            */

            $table->decimal('meals_per_day', 15, 2)->default(0);
            $table->decimal('allowance_per_day', 15, 2)->default(0);
            $table->decimal('local_transport', 15, 2)->default(0);
            $table->decimal('contingencies', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Financial Calculation
            |--------------------------------------------------------------------------
            */

            // Snapshot of the approved SPD balance.
            $table->decimal('balance_received', 15, 2)->default(0);

            // Actual expense:
            // (Meals × Days)
            // + (Allowance × Days)
            // + Local Transport
            // + Contingencies
            $table->decimal('expense_balance', 15, 2)->default(0);

            // Balance Received - Expense Balance
            $table->decimal('expense_report_total', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Report Workflow
            |--------------------------------------------------------------------------
            */

            $table->enum('status_report', [
                'draft',
                'submitted',
                'reviewed',
                'settled',
            ])->default('draft');

            /*
            |--------------------------------------------------------------------------
            | Financial Settlement Result
            |--------------------------------------------------------------------------
            |
            | reimburse       = Company owes employee
            | cash_clear       = No balance remaining
            | refund_employee  = Employee must return money to company
            |
            */

            $table->enum('settlement_status', [
                'reimburse',
                'cash_clear',
                'refund_employee',
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Supporting Evidence
            |--------------------------------------------------------------------------
            */

            // One combined file containing all expense evidence.
            $table->string('expense_evidence')->nullable();

            $table->text('note')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Submission
            |--------------------------------------------------------------------------
            */

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('status_report');
            $table->index('settlement_status');
            $table->index(['employee_id', 'status_report']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spd_reports');
    }
};