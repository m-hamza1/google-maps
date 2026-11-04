<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_paginated_leads(): void
    {
        Lead::create([
            'place_id' => 'place_1',
            'name' => 'Metro Dental Care',
            'primary_phone' => '+1 555-0100',
            'rating' => 4.9,
            'category' => 'Dentist',
            'status' => 'new',
        ]);

        $response = $this->getJson('/api/leads');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Metro Dental Care');
    }

    public function test_can_update_secondary_phone_notes_and_status(): void
    {
        $lead = Lead::create([
            'place_id' => 'place_2',
            'name' => 'Apex Real Estate',
            'primary_phone' => '+1 555-0200',
            'status' => 'new',
        ]);

        $response = $this->patchJson("/api/leads/{$lead->id}", [
            'secondary_phone' => '+1 555-9999',
            'notes' => 'Spoke with CEO, interested in proposal.',
            'status' => 'interested',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.secondary_phone', '+1 555-9999')
            ->assertJsonPath('data.status', 'interested')
            ->assertJsonPath('data.notes', 'Spoke with CEO, interested in proposal.');

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'secondary_phone' => '+1 555-9999',
            'status' => 'interested',
        ]);
    }

    public function test_can_fetch_aggregated_stats(): void
    {
        Lead::create([
            'place_id' => 'place_3',
            'name' => 'City Cafe',
            'primary_phone' => '+1 555-0300',
            'secondary_phone' => '+1 555-0301',
            'status' => 'contacted',
        ]);

        $response = $this->getJson('/api/leads/stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_leads', 1)
            ->assertJsonPath('data.with_secondary_phone', 1)
            ->assertJsonPath('data.statuses.contacted', 1);
    }
}
