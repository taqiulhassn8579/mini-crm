<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $agent;
    protected $lead;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        // Create agent user
        $this->agent = User::factory()->create([
            'role' => 'agent',
            'email' => 'agent@example.com',
        ]);

        // Create a test lead
        $this->lead = Lead::factory()->create([
            'assigned_to' => $this->agent->id,
        ]);
    }

    /** @test */
    public function admin_can_view_all_leads()
    {
        $response = $this->actingAs($this->admin)->get('/leads');

        $response->assertStatus(200);
        $response->assertSee($this->lead->name);
    }

    /** @test */
    public function agent_can_only_view_assigned_leads()
    {
        // Create another lead assigned to admin
        $adminLead = Lead::factory()->create([
            'assigned_to' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->agent)->get('/leads');

        $response->assertStatus(200);
        $response->assertSee($this->lead->name); // Should see assigned lead
        $response->assertDontSee($adminLead->name); // Should not see unassigned lead
    }

    /** @test */
    public function admin_can_create_leads()
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'status' => 'new',
            'assigned_to' => $this->agent->id,
            'notes' => 'Test lead notes',
        ];

        $response = $this->actingAs($this->admin)->post('/leads', $leadData);

        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('leads', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function agent_cannot_create_leads()
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'new',
        ];

        $response = $this->actingAs($this->agent)->post('/leads', $leadData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_lead()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'status' => 'contacted',
        ];

        $response = $this->actingAs($this->admin)->put("/leads/{$this->lead->id}", $updateData);

        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('leads', [
            'id' => $this->lead->id,
            'name' => 'Updated Name',
            'status' => 'contacted',
        ]);
    }

    /** @test */
    public function agent_can_update_assigned_lead()
    {
        $updateData = [
            'name' => 'Updated by Agent',
            'email' => 'agent@example.com',
            'status' => 'contacted',
        ];

        $response = $this->actingAs($this->agent)->put("/leads/{$this->lead->id}", $updateData);

        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('leads', [
            'id' => $this->lead->id,
            'name' => 'Updated by Agent',
        ]);
    }

    /** @test */
    public function agent_cannot_update_unassigned_lead()
    {
        $unassignedLead = Lead::factory()->create([
            'assigned_to' => null,
        ]);

        $updateData = [
            'name' => 'Updated by Agent',
            'email' => 'agent@example.com',
            'status' => 'contacted',
        ];

        $response = $this->actingAs($this->agent)->put("/leads/{$unassignedLead->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_leads()
    {
        $response = $this->actingAs($this->admin)->delete("/leads/{$this->lead->id}");

        $response->assertRedirect('/leads');
        $this->assertSoftDeleted('leads', [
            'id' => $this->lead->id,
        ]);
    }

    /** @test */
    public function agent_cannot_delete_leads()
    {
        $response = $this->actingAs($this->agent)->delete("/leads/{$this->lead->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/leads');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_access_api_endpoints()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/leads');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
        ]);
    }

    /** @test */
    public function lead_validation_works()
    {
        $invalidData = [
            'name' => '', // Required field missing
            'email' => 'invalid-email', // Invalid email
            'status' => 'invalid-status', // Invalid status
        ];

        $response = $this->actingAs($this->admin)->post('/leads', $invalidData);

        $response->assertSessionHasErrors(['name', 'email', 'status']);
    }

    /** @test */
    public function lead_search_and_filtering_works()
    {
        // Create leads with different statuses
        Lead::factory()->create(['status' => 'new', 'name' => 'Searchable Lead']);
        Lead::factory()->create(['status' => 'contacted']);

        $response = $this->actingAs($this->admin)->get('/leads?status=new&search=Searchable');

        $response->assertStatus(200);
        $response->assertSee('Searchable Lead');
    }
}
