<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agenda pimpinan — terhubung dengan delegasi/surat.
     * status: terjadwal | selesai | dibatalkan
     */
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('agenda_type')->nullable()
                ->comment('rapat | undangan | kunjungan | audiensi | pelatihan | lainnya');
            $table->foreignId('delegation_id')->nullable()->constrained('delegations')->nullOnDelete();
            $table->foreignId('letter_id')->nullable()->constrained('letters')->nullOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('terjadwal')
                ->comment('terjadwal | selesai | dibatalkan');
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['date', 'status']);
            $table->index('agenda_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};