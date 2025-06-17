<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Host;
use App\Models\Approver;
use App\Models\Checker;
use App\Models\Form;

class HostResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\HostSeeder::class);
    }

    private function loginAsHost()
    {
        $this->post('/login', [
            'username' => 'host',
            'password' => 'aaspra123',
            'role' => 'host',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_can_access_approver_resource_pages()
    {
        $this->loginAsHost();
        // Index
        $this->get('/host/approvers')->assertStatus(200);
        // Create
        $this->get('/host/approvers/create')->assertStatus(200);
        // Store
        $response = $this->post('/host/approvers', [
            'username' => 'approverbaru',
            'password' => 'password123',
            'role' => 'Penanggung Jawab',
            'status' => 'aktif',
        ]);
        $response->assertRedirect('/host/approvers/create');
        $this->assertDatabaseHas('approvers', ['username' => 'approverbaru']);
        // Edit
        $approver = Approver::where('username', 'approverbaru')->first();
        $this->get('/host/approvers/' . $approver->id . '/edit')->assertStatus(200);
        // Update
        $updateResponse = $this->put('/host/approvers/' . $approver->id, [
            'username' => 'approverbaru_update',
            'role' => 'Kepala Regu',
            'status' => 'tidak_aktif',
        ]);
        $updateResponse->assertRedirect('/host/approvers');
        $this->assertDatabaseHas('approvers', ['username' => 'approverbaru_update', 'status' => 'tidak_aktif']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_can_access_checker_resource_pages()
    {
        $this->loginAsHost();
        // Index
        $this->get('/host/checkers')->assertStatus(200);
        // Create
        $this->get('/host/checkers/create')->assertStatus(200);
        // Store
        $response = $this->post('/host/checkers', [
            'username' => 'checkerbaru',
            'password' => 'password123',
            'status' => 'aktif',
        ]);
        $response->assertRedirect('/host/checkers/create');
        $this->assertDatabaseHas('checkers', ['username' => 'checkerbaru']);
        // Edit
        $checker = Checker::where('username', 'checkerbaru')->first();
        $this->get('/host/checkers/' . $checker->id . '/edit')->assertStatus(200);
        // Update
        $updateResponse = $this->put('/host/checkers/' . $checker->id, [
            'username' => 'checkerbaru_update',
            'status' => 'tidak_aktif',
        ]);
        $updateResponse->assertRedirect('/host/checkers');
        $this->assertDatabaseHas('checkers', ['username' => 'checkerbaru_update', 'status' => 'tidak_aktif']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function host_can_access_form_resource_pages()
    {
        $this->loginAsHost();
        // Index
        $this->get('/host/forms')->assertStatus(200);
        // Create
        $this->get('/host/forms/create')->assertStatus(200);
        // Store
        $response = $this->post('/host/forms', [
            'nomor_form' => 'F-001',
            'nama_form' => 'Form Test',
            'tanggal_efektif' => '2024-01-01',
        ]);
        $response->assertRedirect('/host/forms/create');
        $this->assertDatabaseHas('forms', ['nomor_form' => 'F-001', 'nama_form' => 'Form Test']);
        // Edit
        $form = Form::where('nomor_form', 'F-001')->first();
        $this->get('/host/forms/' . $form->id . '/edit')->assertStatus(200);
        // Update
        $updateResponse = $this->put('/host/forms/' . $form->id, [
            'nomor_form' => 'F-002',
            'nama_form' => 'Form Test Update',
            'tanggal_efektif' => '2024-02-01',
        ]);
        $updateResponse->assertRedirect('/host/forms');
        $this->assertDatabaseHas('forms', ['nomor_form' => 'F-002', 'nama_form' => 'Form Test Update']);
    }
} 