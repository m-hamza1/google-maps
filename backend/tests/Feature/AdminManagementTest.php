<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_pages_render_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_admin_can_register_and_authenticate(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Executive',
            'email' => 'executive@leadradar.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'executive@leadradar.com']);
    }

    public function test_admin_can_delete_single_lead(): void
    {
        $lead = Lead::create([
            'place_id' => 'place_to_delete',
            'name' => 'Delete Me Store',
            'status' => 'new',
        ]);

        $response = $this->delete("/leads/{$lead->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_admin_can_bulk_update_status(): void
    {
        $lead1 = Lead::create(['place_id' => 'p1', 'name' => 'Store 1', 'status' => 'new']);
        $lead2 = Lead::create(['place_id' => 'p2', 'name' => 'Store 2', 'status' => 'new']);

        $response = $this->post('/leads/bulk-action', [
            'bulk_action' => 'update_status',
            'new_status' => 'contacted',
            'selected_ids' => [$lead1->id, $lead2->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('contacted', $lead1->fresh()->status);
        $this->assertEquals('contacted', $lead2->fresh()->status);
    }

    public function test_admin_can_bulk_delete_leads(): void
    {
        $lead1 = Lead::create(['place_id' => 'p3', 'name' => 'Store 3', 'status' => 'new']);
        $lead2 = Lead::create(['place_id' => 'p4', 'name' => 'Store 4', 'status' => 'new']);

        $response = $this->post('/leads/bulk-action', [
            'bulk_action' => 'delete',
            'selected_ids' => [$lead1->id, $lead2->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('leads', ['id' => $lead1->id]);
        $this->assertDatabaseMissing('leads', ['id' => $lead2->id]);
    }

    public function test_ajax_request_returns_table_component_json(): void
    {
        Lead::create(['place_id' => 'ajax_p1', 'name' => 'Ajax Dental Clinic', 'status' => 'new']);

        $response = $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'total', 'stats']);
        $this->assertStringContainsString('Ajax Dental Clinic', $response->json('html'));
    }

    public function test_ajax_lead_update_and_delete(): void
    {
        $lead = Lead::create(['place_id' => 'ajax_p2', 'name' => 'Ajax Cafe', 'status' => 'new']);

        // Test AJAX update
        $updateResp = $this->patch("/leads/{$lead->id}", [
            'status' => 'contacted',
            'secondary_phone' => '+92 300 9999999',
            'notes' => 'Follow up tomorrow',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $updateResp->assertStatus(200);
        $updateResp->assertJson(['success' => true]);
        $this->assertEquals('contacted', $lead->fresh()->status);

        // Test AJAX delete
        $deleteResp = $this->delete("/leads/{$lead->id}", [], ['X-Requested-With' => 'XMLHttpRequest']);
        $deleteResp->assertStatus(200);
        $deleteResp->assertJson(['success' => true]);
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_leads_can_be_filtered_by_city(): void
    {
        Lead::create(['place_id' => 'city_lhr', 'name' => 'Lahore Electronics', 'city' => 'Lahore', 'status' => 'new']);
        Lead::create(['place_id' => 'city_khi', 'name' => 'Karachi Tech Store', 'city' => 'Karachi', 'status' => 'new']);

        $response = $this->get('/?city=Lahore', ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertStatus(200);
        $html = $response->json('html');
        $this->assertStringContainsString('Lahore Electronics', $html);
        $this->assertStringNotContainsString('Karachi Tech Store', $html);
    }
}


