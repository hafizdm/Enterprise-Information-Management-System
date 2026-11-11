<?php

namespace Tests\Feature\Travel;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\Travel\TravelInquiry;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelRequestPassenger;
use App\Models\Travel\TravelVendorControl;
use App\Models\Travel\Vendor;
use App\Models\User;
use App\Services\Travel\VendorLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrdUser;
    protected User $siteUser;
    protected User $adminUser;
    protected Vendor $vendor;
    protected TravelRequest $travelRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $division = Division::create(['name' => 'HR', 'code' => 'HRD', 'is_active' => true]);
        $position = Position::create(['name' => 'Staff', 'code' => 'HRD-01', 'division_id' => $division->id, 'is_active' => true]);

        $project = Project::create([
            'name' => 'Proyek IKN Tol',
            'location' => 'Penajam Paser Utara',
            'cost_center' => 'CC-IKN-01',
        ]);

        // HR/GA HO uses HRD role
        $this->hrdUser = User::create([
            'username' => 'hrd.travel',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // HR/GA Site uses HR/GA Site role
        $this->siteUser = User::create([
            'username' => 'site.hr',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->siteUser->assignRole('HR/GA Site');

        // Administrator without travel permissions
        $this->adminUser = User::create([
            'username' => 'admin.travel',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('System Administrator');

        // Create Active Vendor (Communication via email, no login)
        $this->vendor = Vendor::create([
            'code' => 'VEND-A',
            'name' => 'PT Rekanan Travel Antavaya',
            'email' => 'booking@antavaya.co.id',
            'is_active' => true,
        ]);

        app(VendorLifecycleService::class)->activateVendor($this->vendor->id, '2026-11-01', 'Initial activation', $this->hrdUser->id);

        // Create sample travel request
        $this->travelRequest = TravelRequest::create([
            'request_number' => 'TR-202611-0001',
            'source_type' => 'project',
            'project_id' => $project->id,
            'travel_category' => 'domestic',
            'status' => 'pending',
            'request_date' => now()->toDateString(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'origin' => 'Jakarta (CGK)',
            'destination' => 'Balikpapan (BPN)',
            'need_flight' => 1,
            'created_by' => $this->siteUser->id,
        ]);

        TravelRequestPassenger::create([
            'travel_request_id' => $this->travelRequest->id,
            'passenger_name' => 'Budi Santoso',
            'id_card_type' => 'ktp',
            'id_card_number' => '3171012345678901',
            'phone_number' => '08123456789',
            'gender' => 'male',
        ]);
    }

    public function test_authorized_hrd_user_can_send_inquiry_to_active_vendor(): void
    {
        $payload = [
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
            'deadline_at' => now()->addHours(6)->toDateTimeString(),
            'notes' => 'Tolong berikan opsi penerbangan pagi dan sore via email.',
        ];

        $response = $this->actingAs($this->hrdUser)->post(route('travel.inquiries.store'), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('travel_inquiries', [
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
            'status' => 'sent',
        ]);

        // Travel Request status transitions to in_inquiry
        $this->assertEquals('in_inquiry', $this->travelRequest->fresh()->status);
    }

    public function test_site_user_cannot_send_inquiry_to_vendor(): void
    {
        $payload = [
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
        ];

        // Site HR cannot dispatch inquiries to vendors (reserved for HRD Head Office)
        $response = $this->actingAs($this->siteUser)->post(route('travel.inquiries.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_system_admin_without_travel_permission_cannot_send_inquiry(): void
    {
        $payload = [
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
        ];

        $response = $this->actingAs($this->adminUser)->post(route('travel.inquiries.store'), $payload);
        $response->assertStatus(403);
    }
}
