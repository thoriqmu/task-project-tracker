<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user dummy dan authenticate
        $this->user = User::factory()->create();
    }

    /**
     * Test mendapatkan daftar project
     */
    public function test_can_get_all_projects(): void
    {
        // Setup data dummy
        Project::factory()->count(3)->create([
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => ['id', 'name', 'status', 'created_by']
                     ]
                 ]);
                 
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test create project
     */
    public function test_can_create_project(): void
    {
        $payload = [
            'name' => 'Project Alpha Testing',
            'description' => 'Description test',
            'status' => 'active'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/projects', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Project Alpha Testing',
                         'status' => 'active'
                     ]
                 ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Project Alpha Testing',
        ]);
    }

    /**
     * Test unauthenticated access ditolak
     */
    public function test_unauthenticated_user_cannot_access_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }
}
