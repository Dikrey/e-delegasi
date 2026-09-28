<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom verifikasi sekretaris pada surat masuk.
     * Status: menunggu_verifikasi | terverifikasi | tidak_memerlukan_tindak_lanjut
     *         | memerlukan_tindak_lanjut | didelegasikan | selesai | diarsipkan
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->string('verification_status')->nullable()->default('menunggu_verifikasi')->after('status')
                ->comment('Status verifikasi sekretaris terhadap surat masuk');
            $table->text('verification_note')->nullable()->after('verification_status')
                ->comment('Catatan verifikasi sekretaris');
            $table->foreignId('verified_by')->nullable()->after('verification_note')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verification_note',
                'verified_by',
                'verified_at',
            ]);
        });
    }
};