<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('nik', 50)->unique();
            $table->string('full_name');

            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();

            $table->text('address')->nullable();

            $table->string('religion', 30);
            $table->string('gender', 20);

            $table->string('email')->unique();
            $table->string('phone_number', 30)->nullable();

            $table->string('npwp', 50)->nullable();
            $table->string('bpjs_health', 50)->nullable();
            $table->string('bpjs_employment', 50)->nullable();

            $table->foreignId('division_id')
                ->constrained('divisions')
                ->restrictOnDelete();

            $table->foreignId('position_id')
                ->constrained('positions')
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->string('employee_status', 20);

            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();

            $table->foreignId('report_to')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->decimal('spd_limit', 15, 2)->default(0);

            $table->decimal('total_annual_leave', 5, 2)->default(12);

            $table->string('photo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};