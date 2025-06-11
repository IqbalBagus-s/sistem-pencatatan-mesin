<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Approver;
use App\Models\Checker;

class MachineIndexAccessTest extends TestCase
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
        Approver::create([
            'username' => 'samul',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'approver',
        ]);
        Checker::create([
            'username' => 'jaki',
            'password' => bcrypt('123456'),
            'status' => 'aktif',
            'role' => 'checker',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function approver_can_access_all_machine_index_pages()
    {
        $this->post('/login', [
            'username' => 'samul',
            'password' => '123456',
            'role' => 'approver',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine");
            $response->assertStatus(200, "Approver gagal akses index $machine");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function checker_can_access_all_machine_index_pages()
    {
        $this->post('/login', [
            'username' => 'jaki',
            'password' => '123456',
            'role' => 'checker',
        ]);

        foreach ($this->machines as $machine) {
            $response = $this->get("/$machine");
            $response->assertStatus(200, "Checker gagal akses index $machine");
        }
    }
} 