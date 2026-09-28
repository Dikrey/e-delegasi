<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel task — hasil dari setiap delegasi.
     * Baru | Diterima | Dalam Pengerjaan | Menunggu Review | Selesai | Ditolak | Terlambat
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegation_id')->constrained('delegations')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
            $table->string('priority')->default('normal')
                ->comment('rendah | normal | tinggi | urgent');
            $table->dateTime('deadline')->nullable();
            $table->unsignedSmallInteger('progress')->default(0)->comment('0 - 100');
            $table->string('status')->default('baru')
                ->comment('baru | diterima | dalam_pengerjaan | menunggu_review | selesai | ditolak | terlambat');
            $table->foreignId('agenda_id')->nullable()->comment('Agenda terkait (histori)');
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'staff_id']);
            $table->index(['deadline', 'progress']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};