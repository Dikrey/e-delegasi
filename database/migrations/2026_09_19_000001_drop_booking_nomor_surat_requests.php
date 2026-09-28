<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus sisa fitur "Booking / Permintaan Nomor Surat" yang sudah tidak dipakai.
     *
     * Fitur tersebut sudah dihapus dari routing & antarmuka, namun tabel
     * `nomor_surat_requests` dan kolom `letters.request_id` masih tertinggal.
     */
    public function up(): void
    {
        if (Schema::hasColumn('letters', 'request_id')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->dropConstrainedForeignId('request_id');
            });
        }

        Schema::dropIfExists('nomor_surat_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('nomor_surat_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('room_code')->comment('Kode Ruangan, contoh: 521.1');
            $table->unsignedInteger('requested_qty')->default(1)->comment('Jumlah nomor surat yang diminta');
            $table->date('letter_date')->comment('Tanggal surat pada nomor yang akan diterbitkan');
            $table->string('subject')->comment('Perihal / ringkasan surat');
            $table->string('draft_path')->nullable()->comment('Draf surat yang diunggah');
            $table->string('status')->default('pending')->comment('pending / approved / rejected');
            $table->text('note')->nullable()->comment('Catatan admin saat mereview');
            $table->json('allocated_numbers')->nullable()->comment('Daftar nomor surat yang dialokasikan');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('request_id')->nullable()->after('user_id')
                ->constrained('nomor_surat_requests')->nullOnDelete();
        });
    }
};