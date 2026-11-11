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
        Schema::table('travel_request_passengers', function (Blueprint $table) {
            $table->string('id_card_type', 20)->default('ktp')->after('passenger_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_request_passengers', function (Blueprint $table) {
            $table->dropColumn('id_card_type');
        });
    }
};
