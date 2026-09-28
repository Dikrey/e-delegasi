<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel delegasi — inti dari E-Delegasi.
     * Satu delegasi bersumber dari surat dan menghasilkan satu atau lebih task.
     */
    public function up(): void
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->nullable()->constrained('letters')->nullOnDelete()
                ->comment('Sumber surat');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instruction')->nullable();
            $table->string('priority')->default('normal')
                ->comment('rendah | normal | tinggi | urgent');
            $table->dateTime('deadline')->nullable();
            $table->string('status')->default('draft')
                ->comment('draft | dikirim | diterima | dalam_pengerjaan | menunggu_verifikasi | selesai | ditolak');
            $table->string('attachment')->nullable()
                ->comment('Lampiran delegasi');
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();

            $table->index(['status', 'deadline']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delegations');
    }
};