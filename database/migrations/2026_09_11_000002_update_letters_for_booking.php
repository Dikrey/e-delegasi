<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom status pada tabel surat (letters).
     *
     * Surat keluar disimpan sebagai data sementara (draft) dengan status
     * "waiting_for_final_file" sampai pengguna mengunggah dokumen final
     * yang sudah ditandatangani.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->string('status')->nullable()->after('type')
                ->comment('Status surat (waiting_for_final_file / final)');
        });

        // agenda_number tidak wajib untuk surat dari berstatus draft.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE letters MODIFY agenda_number VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE letters MODIFY agenda_number VARCHAR(255) NOT NULL');
        }
    }
};