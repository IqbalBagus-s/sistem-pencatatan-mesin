<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Checker;

class MachineCreateStoreTest extends TestCase
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
        Checker::create([
            'username' => 'jaki',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'checker',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_can_access_all_machine_create_pages()
    {
        $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine/create");
            $response->assertStatus(200, "Checker gagal akses halaman create $machine");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_cannot_store_machine_data_with_empty_payload()
    {
        $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->post("/$machine", []);
            $response->assertSessionHasErrors(null, "Checker seharusnya gagal store data kosong pada $machine");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_cannot_access_all_machine_create_pages()
    {
        $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine/create");
            $response->assertRedirect('/login');
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_cannot_access_all_machine_create_pages()
    {
        $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine/create");
            $response->assertRedirect('/login');
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_cannot_store_machine_data()
    {
        $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->post("/$machine", []);
            $response->assertRedirect('/login');
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_cannot_store_machine_data()
    {
        $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->post("/$machine", []);
            $response->assertRedirect('/login');
        }
    }
} 