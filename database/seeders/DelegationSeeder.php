<?php

namespace Database\Seeders;

use App\Enums\AgendaStatus;
use App\Enums\DelegationStatus;
use App\Enums\LetterVerification;
use App\Enums\TaskStatus;
use App\Models\Agenda;
use App\Models\Delegation;
use App\Models\Letter;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DelegationSeeder extends Seeder
{
    /**
     * Seed data demo modul E-Delegasi (delegasi, task, progres, agenda, notifikasi).
     */
    public function run(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $admin = User::where('role', 'admin')->first();
        $staffs = User::where('role', 'staff')->get();

        if (!$sekretaris || $staffs->count() < 2) {
            return;
        }

        $letters = Letter::incoming()->latest('letter_date')->take(6)->get();

        // Siapkan sebagian surat agar memerlukan tindak lanjut / sudah didelegasikan.
        foreach ($letters as $index => $letter) {
            Letter::whereKey($letter->id)->update([
                'verification_status' => $index < 2
                    ? LetterVerification::NEEDS_FOLLOW_UP->value
                    : LetterVerification::DELEGATED->value,
                'verified_by' => $sekretaris->id,
                'verified_at' => now(),
            ]);
        }

        $samples = [
            [
                'title' => 'Tindak lanjut undangan rapat koordinasi bidang',
                'description' => 'Menghadiri dan menyusun laporan hasil rapat koordinasi bidang.',
                'instruction' => 'Konfirmasi kehadiran, siapkan materi, dan susun notulen rapat.',
                'priority' => 'tinggi',
                'status' => DelegationStatus::IN_PROGRESS->status(),
                'deadline' => Carbon::now()->addDays(3),
                'task_status' => TaskStatus::IN_PROGRESS->status(),
                'progress' => 45,
            ],
            [
                'title' => 'Penyusunan laporan kinerja triwulan',
                'description' => 'Menyusun rekapitulasi laporan kinerja triwulan berjalan.',
                'instruction' => 'Kumpulkan data dari masing-masing bidang lalu susun rekapitulasi.',
                'priority' => 'normal',
                'status' => DelegationStatus::SENT->status(),
                'deadline' => Carbon::now()->addDays(7),
                'task_status' => TaskStatus::NEW->status(),
                'progress' => 0,
            ],
            [
                'title' => 'Verifikasi berkas permohonan bantuan',
                'description' => 'Memeriksa kelengkapan berkas permohonan bantuan masyarakat.',
                'instruction' => 'Periksa kelengkapan dan buat catatan kekurangan berkas.',
                'priority' => 'urgent',
                'status' => DelegationStatus::PENDING_VERIFICATION->status(),
                'deadline' => Carbon::now()->subDay(),
                'task_status' => TaskStatus::PENDING_REVIEW->status(),
                'progress' => 100,
            ],
            [
                'title' => 'Pengumpulan data potensi pertanian',
                'description' => 'Mengumpulkan data potensi pertanian untuk laporan tahunan.',
                'instruction' => 'Koordinasi dengan penyuluh lapangan untuk data terbaru.',
                'priority' => 'normal',
                'status' => DelegationStatus::DONE->status(),
                'deadline' => Carbon::now()->subDays(3),
                'task_status' => TaskStatus::DONE->status(),
                'progress' => 100,
            ],
            [
                'title' => 'Persiapan kunjungan kerja pimpinan',
                'description' => 'Menyiapkan seluruh kebutuhan kunjungan kerja pimpinan.',
                'instruction' => 'Siapkan jadwal, transportasi, dan bahan paparan.',
                'priority' => 'tinggi',
                'status' => DelegationStatus::ACCEPTED->status(),
                'deadline' => Carbon::now()->addDays(10),
                'task_status' => TaskStatus::ACCEPTED->status(),
                'progress' => 10,
            ],
            [
                'title' => 'Tindak lanjut surat dari Kementerian',
                'description' => 'Menyusun jawaban atas surat permintaan data dari Kementerian.',
                'instruction' => 'Susun draf jawaban dan koordinasikan dengan pimpinan.',
                'priority' => 'urgent',
                'status' => DelegationStatus::REJECTED->status(),
                'deadline' => Carbon::now()->addDays(5),
                'task_status' => TaskStatus::REJECTED->status(),
                'progress' => 0,
            ],
            [
                'title' => 'Draf surat edaran monitoring',
                'description' => 'Menyusun draf surat edaran monitoring kegiatan lapangan.',
                'instruction' => 'Susun draf, ajukan untuk ditandatangani pimpinan.',
                'priority' => 'rendah',
                'status' => DelegationStatus::DRAFT->status(),
                'deadline' => Carbon::now()->addDays(14),
                'task_status' => TaskStatus::NEW->status(),
                'progress' => 0,
            ],
        ];

        foreach ($samples as $index => $sample) {
            $letter = $letters->get($index % max($letters->count(), 1));

            $delegation = Delegation::create([
                'letter_id' => $letter?->id,
                'title' => $sample['title'],
                'description' => $sample['description'],
                'instruction' => $sample['instruction'],
                'priority' => $sample['priority'],
                'deadline' => $sample['deadline'],
                'status' => $sample['status'],
                'created_by' => $sekretaris->id,
            ]);

            $delegation->forceFill([
                'created_at' => Carbon::now()->subDays(10 - $index),
                'updated_at' => Carbon::now()->subDays(2),
            ])->save();

            $assignees = $staffs->slice($index % 2, 2);

            foreach ($assignees as $staff) {
                $task = Task::create([
                    'delegation_id' => $delegation->id,
                    'title' => $sample['title'],
                    'description' => $sample['description'],
                    'staff_id' => $staff->id,
                    'created_by' => $sekretaris->id,
                    'priority' => $sample['priority'],
                    'deadline' => $sample['deadline'],
                    'progress' => $sample['progress'],
                    'status' => $sample['task_status'],
                    'completed_at' => $sample['task_status'] === TaskStatus::DONE->status()
                        ? Carbon::now()->subDays(2)
                        : null,
                    'note' => $sample['task_status'] === TaskStatus::REJECTED->status()
                        ? 'Tidak dapat dikerjakan karena benturan jadwal.'
                        : null,
                ]);

                if ($sample['progress'] > 0) {
                    TaskUpdate::create([
                        'task_id' => $task->id,
                        'user_id' => $staff->id,
                        'progress' => $sample['progress'],
                        'note' => 'Progres pengerjaan diperbarui.',
                    ]);
                }

                if ($sample['status'] !== DelegationStatus::DRAFT->status()) {
                    Notification::create([
                        'user_id' => $staff->id,
                        'type' => 'task',
                        'title' => __('notify.task_new_title'),
                        'message' => __('notify.task_new_message', ['title' => $task->title]),
                        'link' => route('task.show', $task->id),
                    ]);
                }
            }

            if (in_array($sample['status'], [
                DelegationStatus::SENT->status(),
                DelegationStatus::ACCEPTED->status(),
                DelegationStatus::IN_PROGRESS->status(),
                DelegationStatus::PENDING_VERIFICATION->status(),
            ], true)) {
                Agenda::create([
                    'title' => 'Rapat: ' . $sample['title'],
                    'description' => $sample['description'],
                    'agenda_type' => ['rapat', 'undangan', 'audiensi'][$index % 3],
                    'delegation_id' => $delegation->id,
                    'letter_id' => $letter?->id,
                    'date' => Carbon::now()->addDays($index),
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                    'location' => 'Ruang Rapat Utama',
                    'status' => AgendaStatus::SCHEDULED->status(),
                    'created_by' => $admin?->id ?? $sekretaris->id,
                    'note' => 'Agenda tindak lanjut delegasi.',
                ]);
            }
        }
    }
}
