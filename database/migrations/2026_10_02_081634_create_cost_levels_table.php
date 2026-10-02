<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_levels', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->decimal('meals_domestic', 15, 2)->default(0);
            $table->decimal('allowance_domestic', 15, 2)->default(0);

            $table->decimal('meals_international', 15, 2)->default(0);
            $table->decimal('allowance_international', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_levels');
    }
};