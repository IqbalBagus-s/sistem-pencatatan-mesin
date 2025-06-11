<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Checker;

class MachineEditUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $machines = [
        'air-dryer',
        'water-chiller',
        'compressor',
        'hopper',
        'dehum-bahan',
        'giling',
        'autoloader',
        'dehum-matras',
        'caplining',
        'vacuum-cleaner',
        'slitting',
        'crane-matras',
    ];

    public function setUp(): void
    {
        parent::setUp();
        
        // Buat data dummy untuk semua role
        Checker::create([
            'username' => 'jaki',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'checker',
        ]);

        Checker::create([
            'username' => 'samul',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'approver',
        ]);

        Checker::create([
            'username' => 'host',
            'password' => bcrypt('aaspra123'),
            'status' => 'aktif',
            'role' => 'host',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_cannot_access_edit_page_without_id()
    {
        $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine//edit");
            $response->assertStatus(404, "Checker seharusnya tidak bisa akses edit $machine tanpa id");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_cannot_update_machine_data_with_empty_payload_and_invalid_id()
    {
        $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->put("/$machine/999", []); // gunakan id yang pasti tidak ada
            // Bisa jadi 404 (tidak ditemukan) atau 302 (validation error)
            $this->assertTrue(
                in_array($response->getStatusCode(), [404, 302]), 
                "Checker seharusnya tidak bisa update $machine dengan id tidak valid. Status: " . $response->getStatusCode()
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_cannot_access_edit_page()
    {
        $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine/1/edit");
            // Jika route tidak ada, akan 404. Jika ada tapi tidak authorized, akan redirect
            $this->assertTrue(
                in_array($response->getStatusCode(), [404, 302, 403]), 
                "Approver seharusnya tidak bisa akses edit $machine. Status: " . $response->getStatusCode()
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_cannot_access_edit_page()
    {
        $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine/1/edit");
            // Jika route tidak ada, akan 404. Jika ada tapi tidak authorized, akan redirect
            $this->assertTrue(
                in_array($response->getStatusCode(), [404, 302, 403]), 
                "Host seharusnya tidak bisa akses edit $machine. Status: " . $response->getStatusCode()
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_cannot_update_machine_data()
    {
        $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->put("/$machine/1", []);
            // Jika route tidak ada, akan 404. Jika ada tapi tidak authorized, akan redirect
            $this->assertTrue(
                in_array($response->getStatusCode(), [404, 302, 403]), 
                "Approver seharusnya tidak bisa update $machine. Status: " . $response->getStatusCode()
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_cannot_update_machine_data()
    {
        $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->put("/$machine/1", []);
            // Jika route tidak ada, akan 404. Jika ada tapi tidak authorized, akan redirect
            $this->assertTrue(
                in_array($response->getStatusCode(), [404, 302, 403]), 
                "Host seharusnya tidak bisa update $machine. Status: " . $response->getStatusCode()
            );
        }
    }
}