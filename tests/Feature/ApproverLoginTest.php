<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Approver;

class ApproverLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seeder Approver, pastikan user samul tersedia
        Approver::create([
            'username' => 'samul',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'approver',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_can_login_with_valid_credentials_and_redirected_to_dashboard()
    {
        $response = $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated('approver');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_cannot_login_with_invalid_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'samul',
            'password' => 'wrongpassword',
            'role' => 'approver',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Password yang Anda masukkan salah');
        $this->assertGuest('approver');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_role_even_with_correct_username_and_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'checker',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Checker');
        $this->assertGuest('checker');
        $this->assertGuest('approver');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_username_even_with_correct_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'samul_salah',
            'password' => '123456',
            'role' => 'approver',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Approver');
        $this->assertGuest('approver');
    }
} 