<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        try {
            return (bool) DB::table('information_schema.statistics')
                ->where('table_schema', DB::connection()->getDatabaseName())
                ->where('table_name', $table)
                ->where('index_name', $index)
                ->first();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Usir duplikat task lama (satu staff satu task per delegasi),
     * lalu kunci kombinasi (delegation_id, staff_id) biar tidak ada clone lagi.
     */
    public function up()
    {
        // index lama yang menghalangi pembuatan unique constraint (jika ada)
        Schema::table('tasks', function (Blueprint $table) {
            if ($this->indexExists('tasks', 'tasks_staff_id_index')) {
                $table->dropIndex('tasks_staff_id_index');
            }
        });

        // Hapus baris kembar (pertahankan id terkecil); update progres dari baris yang
        // dibuang diwariskan ke baris yang dipertahankan agar tidak hilang.
        $duplicates = DB::table('tasks')
            ->select('delegation_id', 'staff_id')
            ->groupBy('delegation_id', 'staff_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $keepId = (int) DB::table('tasks')
                ->where('delegation_id', $dup->delegation_id)
                ->where('staff_id', $dup->staff_id)
                ->orderBy('id')
                ->value('id');

            $killIds = DB::table('tasks')
                ->where('delegation_id', $dup->delegation_id)
                ->where('staff_id', $dup->staff_id)
                ->where('id', '!=', $keepId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (empty($killIds)) {
                continue;
            }

            DB::table('task_updates')->whereIn('task_id', $killIds)->update(['task_id' => $keepId]);
            DB::table('tasks')->whereIn('id', $killIds)->delete();
        }

        Schema::table('tasks', function (Blueprint $table) {
            if ($this->indexExists('tasks', 'tasks_delegation_id_staff_id_unique')) {
                $table->dropUnique('tasks_delegation_id_staff_id_unique');
            }
            $table->unique(['delegation_id', 'staff_id'], 'tasks_delegation_id_staff_id_unique');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropUnique('tasks_delegation_id_staff_id_unique');
        });
    }
};