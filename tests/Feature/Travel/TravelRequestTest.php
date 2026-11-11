<?php

namespace Tests\Feature\Travel;

use App\Jobs\Travel\SyncApprovedSpdToTravelJob;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\Spd;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelRequestPassenger;
use App\Models\User;
use App\Services\Travel\TravelRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrdUser;
    protected User $siteUser;
    protected User $adminUser;
    protected User $regularEmployeeUser;
    protected Project $testProject;
    protected Employee $employeeTraveler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $division = Division::create(['name' => 'Engineering', 'code' => 'ENG', 'is_active' => true]);
        $position = Position::create(['name' => 'Engineer', 'code' => 'ENG-01', 'division_id' => $division->id, 'is_active' => true]);

        $this->testProject = Project::create([
            'name' => 'Proyek IKN Tol',
            'location' => 'Penajam Paser Utara',
            'cost_center' => 'CC-IKN-01',
        ]);

        $this->employeeTraveler = Employee::create([
            'nik' => 'EMP-TRV-001',
            'full_name' => 'Budi Santoso',
            'email' => 'budi.santoso@rapid-infra.co.id',
            'religion' => 'Islam',
            'gender' => 'male',
            'phone_number' => '08123456789',
            'division_id' => $division->id,
            'position_id' => $position->id,
            'project_id' => $this->testProject->id,
            'employee_status' => 'permanent',
        ]);

        // HR/GA HO uses existing HRD role
        $this->hrdUser = User::create([
            'username' => 'hrd.travel',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // Site HR/GA uses role HR/GA Site
        $this->siteUser = User::create([
            'username' => 'site.hr',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->siteUser->assignRole('HR/GA Site');

        // Administrator without travel operational access
        $this->adminUser = User::create([
            'username' => 'admin.system',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('System Administrator');

        $this->regularEmployeeUser = User::create([
            'username' => 'regular.user',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->regularEmployeeUser->assignRole('Employee');
    }

    public function test_guest_is_redirected_to_login_from_travel_requests(): void
    {
        $response = $this->get(route('travel.requests.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_central_queue(): void
    {
        $response = $this->actingAs($this->regularEmployeeUser)->get(route('travel.requests.index'));
        $response->assertStatus(403);
    }

    public function test_system_admin_without_travel_permission_cannot_access_central_queue(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('travel.requests.index'));
        $response->assertStatus(403);
    }

    public function test_site_hr_role_can_view_central_queue(): void
    {
        $response = $this->actingAs($this->siteUser)->get(route('travel.requests.index'));
        $response->assertStatus(200);
        $response->assertSee('Antrean Permintaan Perjalanan');
    }

    public function test_hrd_ho_role_can_view_central_queue(): void
    {
        $response = $this->actingAs($this->hrdUser)->get(route('travel.requests.index'));
        $response->assertStatus(200);
        $response->assertSee('Antrean Permintaan Perjalanan');
    }

    public function test_site_user_can_create_project_travel_request_with_manual_multi_passenger_input(): void
    {
        // Notice: Passengers are completely manually input, NO employee_id or relation to employees
        $payload = [
            'project_id' => $this->testProject->id,
            'travel_category' => 'domestic',
            'request_date' => now()->toDateString(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'departure_time_window' => 'Pagi 07:00 - 10:00',
            'return_date' => now()->addDays(10)->toDateString(),
            'return_time_window' => 'Sore 16:00 - 19:00',
            'origin' => 'Jakarta (CGK)',
            'destination' => 'Balikpapan (BPN)',
            'need_flight' => 1,
            'need_hotel' => 1,
            'need_local_transport' => 1,
            'notes' => 'Mobilisasi tim konstruksi lapangan.',
            'passengers' => [
                [
                    'passenger_name' => 'Dr. Robert Kurniawan',
                    'id_card_type' => 'ktp',
                    'id_card_number' => '3171012345678901',
                    'gender' => 'male',
                    'phone_number' => '08123456789',
                    'specific_needs' => 'Bagasi 20kg alat ukur',
                ],
                [
                    'passenger_name' => 'Michael Smith (Expert Consultant)',
                    'id_card_type' => 'passport',
                    'id_card_number' => 'A12345678',
                    'gender' => 'male',
                    'phone_number' => '+628198765432',
                    'specific_needs' => 'Vegetarian meal',
                ],
            ],
        ];

        $response = $this->actingAs($this->siteUser)->post(route('travel.requests.store'), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('travel_requests', [
            'source_type' => 'project',
            'project_id' => $this->testProject->id,
            'origin' => 'Jakarta (CGK)',
            'destination' => 'Balikpapan (BPN)',
            'status' => 'pending',
            'need_flight' => 1,
            'need_hotel' => 1,
            'need_local_transport' => 1,
            'created_by' => $this->siteUser->id,
        ]);

        $travelRequest = TravelRequest::where('source_type', 'project')->first();
        $this->assertNotNull($travelRequest);
        $this->assertCount(2, $travelRequest->passengers);

        // Verify manual input data in passengers table
        $p1 = $travelRequest->passengers->where('passenger_name', 'Dr. Robert Kurniawan')->first();
        $this->assertNotNull($p1);
        $this->assertEquals('ktp', $p1->id_card_type);
        $this->assertEquals('3171012345678901', $p1->id_card_number);
        $this->assertNull($p1->employee_id); // No employee relation required

        $p2 = $travelRequest->passengers->where('passenger_name', 'Michael Smith (Expert Consultant)')->first();
        $this->assertNotNull($p2);
        $this->assertEquals('passport', $p2->id_card_type);
        $this->assertEquals('A12345678', $p2->id_card_number);
        $this->assertEquals('Vegetarian meal', $p2->specific_needs);
        $this->assertNull($p2->employee_id);
    }

    public function test_spd_sync_job_creates_travel_request_idempotently(): void
    {
        $spd = Spd::create([
            'spd_number' => 'SPD/2026/11/0001',
            'document_year' => 2026,
            'document_sequence' => 1,
            'employee_id' => $this->employeeTraveler->id,
            'project_id' => $this->testProject->id,
            'manager_id' => $this->employeeTraveler->id,
            'approval_document_id' => $this->employeeTraveler->id,
            'travel_type' => 'domestic',
            'from' => 'Jakarta',
            'destination' => 'Surabaya',
            'date_departure' => now()->addDays(3)->toDateString(),
            'date_return' => now()->addDays(7)->toDateString(),
            'total_days' => 4,
            'transportation' => 'plane',
            'purpose' => 'Inspeksi berkala',
            'status' => 'approved',
            'created_by' => $this->hrdUser->id,
        ]);

        // Dispatch sync job
        $service = app(TravelRequestService::class);
        $job = new SyncApprovedSpdToTravelJob($spd->id);
        $job->handle($service);

        $this->assertDatabaseHas('travel_requests', [
            'source_type' => 'spd',
            'spd_id' => $spd->id,
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'need_flight' => 1,
            'status' => 'pending',
        ]);

        $tr = TravelRequest::where('spd_id', $spd->id)->first();
        $this->assertNotNull($tr);
        $this->assertCount(1, $tr->passengers);
        $this->assertEquals('Budi Santoso', $tr->passengers->first()->passenger_name);

        // Run again to verify idempotency
        $job->handle($service);
        $this->assertEquals(1, TravelRequest::where('spd_id', $spd->id)->count());
    }

    public function test_reconcile_command_syncs_missing_approved_spds(): void
    {
        $spd = Spd::create([
            'spd_number' => 'SPD/RECON/001',
            'document_year' => 2026,
            'document_sequence' => 2,
            'employee_id' => $this->employeeTraveler->id,
            'project_id' => $this->testProject->id,
            'manager_id' => $this->employeeTraveler->id,
            'approval_document_id' => $this->employeeTraveler->id,
            'travel_type' => 'domestic',
            'from' => 'Bandung',
            'destination' => 'Medan',
            'date_departure' => now()->addDays(2)->toDateString(),
            'date_return' => now()->addDays(5)->toDateString(),
            'total_days' => 3,
            'transportation' => 'plane',
            'purpose' => 'Reconciliation test',
            'status' => 'pending_manager',
            'created_by' => $this->hrdUser->id,
        ]);

        Spd::where('id', $spd->id)->update(['status' => 'approved']);

        $this->assertDatabaseMissing('travel_requests', ['spd_id' => $spd->id]);

        $this->artisan('travel:reconcile-spd-requests')
            ->assertExitCode(0);

        $this->assertDatabaseHas('travel_requests', [
            'spd_id' => $spd->id,
            'origin' => 'Bandung',
            'destination' => 'Medan',
        ]);
    }
}
