<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * Add the numbering columns first as nullable.
         *
         * This is necessary because the spds table may already
         * contain existing records.
         */
        Schema::table('spds', function (Blueprint $table) {
            $table->string('spd_number', 50)
                ->nullable()
                ->after('id');

            $table->unsignedInteger('document_year')
                ->nullable()
                ->after('spd_number');

            $table->unsignedInteger('document_sequence')
                ->nullable()
                ->after('document_year');
        });

        /*
         * Backfill existing SPD records.
         *
         * Existing SPD records use their created_at date to determine:
         * - document_year
         * - month inside spd_number
         *
         * Sequence is reset every year.
         */
        $sequenceByYear = [];

        DB::table('spds')
            ->orderBy('id')
            ->get()
            ->each(function ($spd) use (&$sequenceByYear) {
                $createdAt = $spd->created_at
                    ? \Carbon\Carbon::parse($spd->created_at)
                    : \Carbon\Carbon::now();

                $year = (int) $createdAt->year;
                $month = $createdAt->format('m');

                if (!isset($sequenceByYear[$year])) {
                    $sequenceByYear[$year] = 0;
                }

                $sequenceByYear[$year]++;

                $sequence = $sequenceByYear[$year];

                $spdNumber = sprintf(
                    'RII/HC-SPD/%s/%d/%03d',
                    $month,
                    $year,
                    $sequence
                );

                DB::table('spds')
                    ->where('id', $spd->id)
                    ->update([
                        'spd_number' => $spdNumber,
                        'document_year' => $year,
                        'document_sequence' => $sequence,
                    ]);
            });

        /*
         * Make the numbering columns mandatory after all
         * existing records have been populated.
         */
        Schema::table('spds', function (Blueprint $table) {
            $table->string('spd_number', 50)
                ->nullable(false)
                ->change();

            $table->unsignedInteger('document_year')
                ->nullable(false)
                ->change();

            $table->unsignedInteger('document_sequence')
                ->nullable(false)
                ->change();

            /*
             * SPD number must always be unique.
             */
            $table->unique('spd_number');

            /*
             * The sequence cannot be duplicated within the same year.
             */
            $table->unique(
                ['document_year', 'document_sequence'],
                'spds_year_sequence_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spds', function (Blueprint $table) {
            $table->dropUnique('spds_year_sequence_unique');
            $table->dropUnique(['spd_number']);

            $table->dropColumn([
                'spd_number',
                'document_year',
                'document_sequence',
            ]);
        });
    }
};