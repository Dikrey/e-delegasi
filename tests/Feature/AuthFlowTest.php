<?php

namespace Tests\Feature;

use App\Models\LoginSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createSession(User $user, string $sid, string $ip = '203.0.113.7'): LoginSession
    {
        return LoginSession::create([
            'user_id' => $user->id,
            'session_id' => $sid,
            'ip_address' => $ip,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'device' => 'windows',
            'browser' => 'chrome',
            'platform' => 'Windows',
            'location' => 'Palo Alto, US',
            'last_activity_at' => now(),
        ]);
    }

    private function withSessionCookie(string $sid)
    {
        return $this->withCookie(config('session.cookie'), $sid);
    }

    /** @test */
    public function login_page_is_accessible()
    {
        $this->get(route('login'))->assertOk()->assertSee('email');
    }

    /** @test */
    public function blocked_page_is_accessible()
    {
        $this->get(route('blocked'))->assertOk();
    }

    /** @test */
    public function admin_can_login_and_session_is_recorded()
    {
        $this->post(route('login'), [
            'email' => 'admin@example.test',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->admin);
        $this->assertDatabaseHas('login_sessions', ['user_id' => $this->admin->id]);
    }

    /** @test */
    public function inactive_user_is_sent_to_blocked_page()
    {
        $this->admin->update(['is_active' => false]);

        $this->post(route('login'), [
            'email' => 'admin@example.test',
            'password' => 'password',
        ])->assertRedirect(route('blocked'));

        $this->assertGuest();
    }

    /** @test */
    public function deactivated_account_is_forced_to_blocked_page()
    {
        $sid = str_repeat('a', 40);
        $this->createSession($this->admin, $sid);

        $this->admin->update(['is_active' => false]);

        $this->withSessionCookie($sid)
            ->actingAs($this->admin)
            ->get(route('home'))
            ->assertRedirect(route('blocked'));

        $this->assertGuest();
    }

    /** @test */
    public function password_change_revokes_all_sessions()
    {
        $sidA = str_repeat('a', 40);
        $sidB = str_repeat('b', 40);
        $this->createSession($this->admin, $sidA);
        $this->createSession($this->admin, $sidB);

        $this->withSessionCookie($sidA)
            ->actingAs($this->admin)
            ->put(route('user.update', $this->admin), [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertSame(0, $this->admin->fresh()->loginSessions()->count());
        $this->assertDatabaseMissing('login_sessions', ['user_id' => $this->admin->id]);
    }

    /** @test */
    public function per_device_logout_revokes_only_targeted_session()
    {
        $currentSid = str_repeat('c', 40);
        $current = $this->createSession($this->admin, $currentSid);
        $other = $this->createSession($this->admin, str_repeat('d', 40));

        $this->withSessionCookie($currentSid)
            ->actingAs($this->admin)
            ->post(route('devices.logout', $other))
            ->assertRedirect();

        $this->assertDatabaseMissing('login_sessions', ['id' => $other->id]);
        $this->assertDatabaseHas('login_sessions', ['id' => $current->id]);
    }

    /** @test */
    public function logout_revokes_current_session()
    {
        $sid = str_repeat('e', 40);
        $this->createSession($this->admin, $sid);

        $this->withSessionCookie($sid)
            ->actingAs($this->admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('login_sessions', ['user_id' => $this->admin->id]);
    }
}