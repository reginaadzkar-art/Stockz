<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_owner_can_access_dashboard_and_reports(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $dashboardResponse = $this->actingAs($owner)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);

        $reportResponse = $this->actingAs($owner)->get(route('reports.stock'));
        $reportResponse->assertStatus(200);
    }

    public function test_staff_cannot_see_omset_and_financial_valuations(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $owner = User::factory()->create(['role' => 'owner']);

        // Check Dashboard for Staff
        $staffDashboard = $this->actingAs($staff)->get(route('dashboard'));
        $staffDashboard->assertStatus(200);
        $staffDashboard->assertDontSee('ESTIMASI VALUASI INVENTORY');
        $staffDashboard->assertSee('DASHBOARD OPERASIONAL STOK');

        // Check Dashboard for Owner
        $ownerDashboard = $this->actingAs($owner)->get(route('dashboard'));
        $ownerDashboard->assertStatus(200);
        $ownerDashboard->assertSee('ESTIMASI VALUASI INVENTORY');

        // Check Stock Movements for Staff
        $staffMovements = $this->actingAs($staff)->get(route('stock-movements.index'));
        $staffMovements->assertStatus(200);
        $staffMovements->assertDontSee('Total Nilai Nominal');
    }
}
