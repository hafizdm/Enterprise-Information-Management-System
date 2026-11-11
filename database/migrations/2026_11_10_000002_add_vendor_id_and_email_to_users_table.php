<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['internal', 'vendor'])
                ->default('internal')
                ->after('id');

            $table->foreignId('vendor_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('vendors')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('email')
                ->nullable()
                ->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['user_type', 'vendor_id', 'email']);
        });
    }
};
