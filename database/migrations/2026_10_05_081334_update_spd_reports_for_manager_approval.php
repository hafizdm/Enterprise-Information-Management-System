<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spd_reports', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Manager Approval
            |--------------------------------------------------------------------------
            */

            $table->timestamp('manager_approved_at')
                ->nullable()
                ->after('submitted_at');

            $table->text('manager_rejection_reason')
                ->nullable()
                ->after('manager_approved_at');

            /*
            |--------------------------------------------------------------------------
            | Report Workflow Status
            |--------------------------------------------------------------------------
            |
            | Current:
            | draft
            | submitted
            | reviewed
            | settled
            |
            | New:
            | draft
            | submitted
            | approved
            | rejected
            | settled
            |
            */

            $table->index('manager_approved_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Change Status Enum
        |--------------------------------------------------------------------------
        |
        | MySQL requires changing the enum definition explicitly.
        |
        */

        DB::statement("
            ALTER TABLE spd_reports
            MODIFY status_report ENUM(
                'draft',
                'submitted',
                'approved',
                'rejected',
                'settled'
            ) NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Restore Previous Status Enum
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE spd_reports
            MODIFY status_report ENUM(
                'draft',
                'submitted',
                'reviewed',
                'settled'
            ) NOT NULL DEFAULT 'draft'
        ");

        Schema::table('spd_reports', function (Blueprint $table) {
            $table->dropIndex([
                'manager_approved_at',
            ]);

            $table->dropColumn([
                'manager_approved_at',
                'manager_rejection_reason',
            ]);
        });
    }
};
