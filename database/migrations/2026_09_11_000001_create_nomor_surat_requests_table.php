<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_requests');
    }
};