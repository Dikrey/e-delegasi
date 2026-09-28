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
        // Add customisable code_part column to letter-request table.
        if (!Schema::hasColumn('nomor_surat_requests', 'code_part')) {
            Schema::table('nomor_surat_requests', function (Blueprint $table) {
                $table->string('code_part', 20)->default('SK')->after('room_code');
            });
        }

        // Prevent duplicate reference numbers at the database level when the
        // current data set has no collisions and no empty values. Deleted
        // numbers remain reusable because a hard delete removes the row,
        // freeing the value for future allocation.
        $emptyCount = DB::table('letters')
            ->where(function ($query) {
                $query->whereNull('reference_number')
                    ->orWhere('reference_number', '=', '');
            })->count();

        $duplicateCount = DB::table('letters')
            ->whereNotNull('reference_number')
            ->where('reference_number', '<>', '')
            ->groupBy('reference_number')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $uniqueIndexExists = DB::selectOne(
            "SELECT 1 AS exists_flag FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'letters' AND COLUMN_NAME = 'reference_number' AND NON_UNIQUE = 0 LIMIT 1"
        );

        if (!$emptyCount && !$duplicateCount && !$uniqueIndexExists) {
            Schema::table('letters', function (Blueprint $table) {
                $table->unique('reference_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('nomor_surat_requests', 'code_part')) {
            Schema::table('nomor_surat_requests', function (Blueprint $table) {
                $table->dropColumn('code_part');
            });
        }

        $uniqueIndexExists = DB::selectOne(
            "SELECT 1 AS exists_flag FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'letters' AND COLUMN_NAME = 'reference_number' AND NON_UNIQUE = 0 LIMIT 1"
        );

        if ($uniqueIndexExists) {
            Schema::table('letters', function (Blueprint $table) {
                $table->dropIndex(['reference_number']);
            });
        }
    }
};
