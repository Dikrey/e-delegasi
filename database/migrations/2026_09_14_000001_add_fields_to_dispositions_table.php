<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom untuk lembar disposisi baru:
     * - Diteruskan kepada (forwarded_to) + UPT custom (forwarded_to_custom)
     * - Dengan hormat harap (honor) + isi sendiri (honor_custom)
     * - Instruksi panjang (instruction)
     * - Verifikasi sekretaris (direction, is_received, verification_note,
     *   verified_by, verified_at)
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->json('forwarded_to')->nullable()->after('to')
                ->comment('Diteruskan kepada (daftar kunci pihak tujuan)');
            $table->string('forwarded_to_custom')->nullable()->after('forwarded_to')
                ->comment('UPT ........ isi sendiri');
            $table->json('honor')->nullable()->after('content')
                ->comment('Dengan hormat harap (daftar kunci perlakuan)');
            $table->string('honor_custom')->nullable()->after('honor')
                ->comment('Dengan hormat harap - isi sendiri');
            $table->text('instruction')->nullable()->after('note')
                ->comment('Instruksi panjang untuk penerima disposisi');

            $table->string('direction')->nullable()->after('instruction')
                ->comment('Arah disposisi hasil verifikasi sekretaris');
            $table->boolean('is_received')->nullable()->after('direction')
                ->comment('Apakah surat diterima oleh pihak tujuan (verifikasi sekretaris)');
            $table->text('verification_note')->nullable()->after('is_received')
                ->comment('Catatan verifikasi sekretaris');
            $table->foreignId('verified_by')->nullable()->after('verification_note')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'forwarded_to',
                'forwarded_to_custom',
                'honor',
                'honor_custom',
                'instruction',
                'direction',
                'is_received',
                'verification_note',
                'verified_at',
            ]);
        });
    }
};