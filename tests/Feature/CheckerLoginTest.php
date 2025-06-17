<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Checker;

class CheckerLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seeder Checker, pastikan user jaki tersedia
        Checker::create([
            'username' => 'jaki',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'checker',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_can_login_with_valid_credentials_and_redirected_to_dashboard()
    {
        $response = $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated('checker');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_cannot_login_with_invalid_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'jaki',
            'password' => 'wrongpassword',
            'role' => 'checker',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Password yang Anda masukkan salah');
        $this->assertGuest('checker');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_role_even_with_correct_username_and_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'approver',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Approver');
        $this->assertGuest('approver');
        $this->assertGuest('checker');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_username_even_with_correct_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'jaki_salah',
            'password' => '123456',
            'role' => 'checker',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Checker');
        $this->assertGuest('checker');
    }
} 