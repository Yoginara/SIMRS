<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMRS Medika');
        $response->assertSee('admin@simrs.test');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@simrs.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@simrs.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::where('email', 'admin@simrs.test')->first();
        $user->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'admin@simrs.test',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::where('email', 'admin@simrs.test')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_for_all_roles(): void
    {
        $roles = [
            'admin@simrs.test' => 'Total Pasien Terdaftar',
            'dokter@simrs.test' => 'Pasien Dalam Antrean Hari Ini',
            'farmasi@simrs.test' => 'Resep Menunggu Validasi',
            'kasir@simrs.test' => 'Tagihan Belum Lunas',
        ];

        foreach ($roles as $email => $expectedText) {
            $user = User::where('email', $email)->first();
            $response = $this->actingAs($user)->get('/dashboard');

            $response->assertStatus(200);
            $response->assertSee($expectedText);
        }
    }
}
