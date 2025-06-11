<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Host;

class HostLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Jalankan seeder Host agar user host tersedia
        $this->seed(\Database\Seeders\HostSeeder::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_can_login_with_valid_credentials_and_redirected_to_dashboard()
    {
        $response = $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        $response->assertRedirect(route('host.dashboard'));
        $this->assertAuthenticated('host');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_cannot_login_with_invalid_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'host',
            'password' => 'wrongpassword',
            'role' => 'host',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Password yang Anda masukkan salah');
        $this->assertGuest('host');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_role_even_with_correct_username_and_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'checker', // seharusnya host
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Checker');
        $this->assertGuest('checker');
        $this->assertGuest('host');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_login_with_wrong_username_even_with_correct_password()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'host_salah',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Username tidak ditemukan untuk posisi Host');
        $this->assertGuest('host');
    }
} 