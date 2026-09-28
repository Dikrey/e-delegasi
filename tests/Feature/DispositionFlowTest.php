<?php

namespace Tests\Feature;

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\LoginSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DispositionFlowTest extends TestCase
{
    use DatabaseTransactions;

    private User $sekretaris;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekretaris = User::factory()->create([
            'name' => 'Sekretaris Test',
            'email' => 'sekretaris@example.test',
            'role' => 'sekretaris',
            'is_active' => true,
        ]);

        LoginSession::create([
            'user_id' => $this->sekretaris->id,
            'session_id' => str_repeat('s', 40),
            'ip_address' => '203.0.113.7',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'device' => 'windows',
            'browser' => 'chrome',
            'platform' => 'Windows',
            'last_activity_at' => now(),
        ]);
    }

    private function authed(?string $sid = null)
    {
        return $this->withCookie(config('session.cookie'), $sid ?? str_repeat('s', 40))
            ->actingAs($this->sekretaris);
    }

    /** @test */
    public function sekretaris_can_open_disposition_index_and_create()
    {
        $letter = Letter::factory()->create(['type' => 'incoming', 'user_id' => $this->sekretaris->id]);

        $this->authed()->get(route('transaction.disposition.index', $letter))->assertOk();
        $this->authed()->get(route('transaction.disposition.create', $letter))->assertOk();
    }

    /** @test */
    public function staff_can_view_transaction_but_cannot_verify_disposition()
    {
        $staff = User::factory()->create(['role' => 'staff', 'is_active' => true]);
        LoginSession::create([
            'user_id' => $staff->id,
            'session_id' => str_repeat('q', 40),
            'last_activity_at' => now(),
        ]);

        $letter = Letter::factory()->create(['type' => 'incoming', 'user_id' => $this->sekretaris->id]);
        $disposition = Disposition::create([
            'to' => 'Kepala Bidang',
            'due_date' => '2026-10-01',
            'content' => 'Isi disposisi test',
            'letter_status' => \App\Models\LetterStatus::value('id'),
            'letter_id' => $letter->id,
            'user_id' => $this->sekretaris->id,
        ]);

        $request = $this->withCookie(config('session.cookie'), str_repeat('q', 40))
            ->actingAs($staff);

        $request->get(route('transaction.incoming.index'))->assertOk();
        $request->get(route('transaction.disposition.verify', [$letter, $disposition]))->assertForbidden();
    }

    /** @test */
    public function disposition_can_be_stored_with_new_fields()
    {
        $letter = Letter::factory()->create(['type' => 'incoming', 'user_id' => $this->sekretaris->id]);

        $this->authed()->post(route('transaction.disposition.store', $letter), [
            'to' => 'Kepala Bidang',
            'due_date' => '2026-10-01',
            'received_at' => '2026-09-10',
            'content' => 'Isi disposisi test',
            'letter_status' => \App\Models\LetterStatus::value('id'),
            'note' => 'Catatan',
            'forwarded_to' => ['sekretaris', 'kabid_ketahanan_pangan'],
            'forwarded_to_custom' => '',
            'honor' => ['tindak_lanjut', 'custom'],
            'honor_custom' => 'Jelaskan secara tertulis',
            'instruction' => 'Intruksi panjang untuk penerima.',
        ])->assertRedirect(route('transaction.disposition.index', $letter));

        $disposition = $letter->dispositions()->latest()->first();
        $this->assertSame(['sekretaris', 'kabid_ketahanan_pangan'], $disposition->forwarded_to);
        $this->assertSame(['tindak_lanjut', 'custom'], $disposition->honor);
        $this->assertSame('Intruksi panjang untuk penerima.', $disposition->instruction);
        $this->assertStringContainsString('Intruksi panjang', $disposition->instruction);
        $this->assertContains('Kabid Ketahanan Pangan', $disposition->forwarded_labels);
        $this->assertContains('Tindak Lanjut', $disposition->honor_labels);
        $this->assertSame('2026-09-10', $disposition->received_at?->format('Y-m-d'));
    }

    /** @test */
    public function print_and_verify_pages_render()
    {
        $letter = Letter::factory()->create(['type' => 'incoming', 'user_id' => $this->sekretaris->id]);
        $disposition = Disposition::create([
            'to' => 'Kepala Bidang',
            'due_date' => '2026-10-01',
            'received_at' => '2026-09-10',
            'content' => 'Isi disposisi test',
            'note' => 'Catatan',
            'letter_status' => \App\Models\LetterStatus::value('id'),
            'letter_id' => $letter->id,
            'user_id' => $this->sekretaris->id,
            'forwarded_to' => ['sekretaris', 'kabid_ketahanan_pangan'],
            'honor' => ['tindak_lanjut'],
            'instruction' => 'Instruksi.',
        ]);

        $this->authed()->get(route('transaction.disposition.print', [$letter, $disposition]))
            ->assertOk()
            ->assertSee('LEMBAR DISPOSISI')
            ->assertSee('Dinas Pertanian dan Ketahanan Pangan')
            ->assertSee('Tanggal Diterima')
            ->assertSee('dicetak otomatis');

        $this->authed()->get(route('transaction.disposition.verify', [$letter, $disposition]))
            ->assertOk()
            ->assertSee('Verifikasi Lembar Disposisi');
    }

    /** @test */
    public function sekretaris_can_verify_disposition()
    {
        $letter = Letter::factory()->create(['type' => 'incoming', 'user_id' => $this->sekretaris->id]);
        $disposition = Disposition::create([
            'to' => 'Kepala Bidang',
            'due_date' => '2026-10-01',
            'content' => 'Isi disposisi test',
            'letter_status' => \App\Models\LetterStatus::value('id'),
            'letter_id' => $letter->id,
            'user_id' => $this->sekretaris->id,
        ]);

        $this->authed()->post(route('transaction.disposition.verified', [$letter, $disposition]), [
            'direction' => 'Kabid Tanaman Pangan',
            'is_received' => '1',
            'verification_note' => 'Diterima untuk ditindaklanjuti.',
        ])->assertRedirect(route('transaction.disposition.index', $letter));

        $fresh = $disposition->fresh();
        $this->assertTrue($fresh->is_received);
        $this->assertSame('Kabid Tanaman Pangan', $fresh->direction);
        $this->assertSame($this->sekretaris->id, $fresh->verified_by);
        $this->assertNotNull($fresh->verified_at);
    }
}