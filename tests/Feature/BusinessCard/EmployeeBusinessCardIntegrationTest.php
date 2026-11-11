<?php

namespace Tests\Feature\BusinessCard;

use App\Models\CostLevel;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeBusinessCard;
use App\Models\Position;
use App\Models\User;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeBusinessCardIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected DigitalBusinessCardService $service;
    protected Division $division;
    protected Position $position;
    protected CostLevel $costLevel;

    protected User $adminUser;
    protected User $hrdUser;
    protected User $siteUser;
    protected User $regularEmployeeUser;
    protected User $viewOnlyUser;

    protected Employee $testEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->service = new DigitalBusinessCardService();

        $this->division = Division::create([
            'name' => 'Human Resources',
            'code' => 'HR',
            'is_active' => true,
        ]);

        $this->position = Position::create([
            'name' => 'HR Officer',
            'division_id' => $this->division->id,
            'description' => 'HR Officer Description',
        ]);

        $this->costLevel = CostLevel::create([
            'name' => 'Staff',
            'is_active' => true,
        ]);

        $this->testEmployee = Employee::create([
            'nik' => 'EMP-TEST-001',
            'full_name' => 'Budi Santoso',
            'email' => 'budi.santoso@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'phone_number' => '081234567890',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Permanent',
            'spd_limit' => 5000000,
            'total_annual_leave' => 12,
        ]);

        // 1. System Administrator (has employee.view-any and employee.update-any)
        $this->adminUser = User::create([
            'username' => 'admin.user',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('System Administrator');

        // 2. HRD (has employee.view-any and employee.update-any)
        $this->hrdUser = User::create([
            'username' => 'hrd.user',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // 3. HR/GA Site (only travel & SPD permissions, NO employee.view-any)
        $this->siteUser = User::create([
            'username' => 'site.user',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->siteUser->assignRole('HR/GA Site');

        // 4. Employee (only employee.view-own, NO employee.view-any)
        $this->regularEmployeeUser = User::create([
            'username' => 'regular.emp',
            'password' => bcrypt('password'),
            'employee_id' => $this->testEmployee->id,
            'is_active' => true,
        ]);
        $this->regularEmployeeUser->assignRole('Employee');

        // 5. View-Only User (e.g., Board of Director - has employee.view-any but NOT employee.update-any)
        $this->viewOnlyUser = User::create([
            'username' => 'bod.user',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->viewOnlyUser->assignRole('Board of Director');
    }

    /**
     * Test HRD can access the business card modal endpoint.
     */
    public function test_hrd_can_access_business_card_modal(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertViewIs('hr.employees.partials.business_card_modal');
        $response->assertSee($this->testEmployee->full_name);
        $response->assertSee('Digital Business Card');
    }

    /**
     * Test System Administrator can access the business card modal endpoint.
     */
    public function test_system_administrator_can_access_business_card_modal(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertSee('Digital Business Card');
    }

    /**
     * Test HR/GA Site is denied access (403 Forbidden).
     */
    public function test_hr_ga_site_is_forbidden(): void
    {
        $response = $this->actingAs($this->siteUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(403);
    }

    /**
     * Test ordinary Employee role is denied access (403 Forbidden).
     */
    public function test_employee_role_is_forbidden(): void
    {
        $response = $this->actingAs($this->regularEmployeeUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(403);
    }

    /**
     * Test unauthenticated guest is redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test modal gets or creates business card record idempotently.
     */
    public function test_modal_gets_or_creates_card_idempotently(): void
    {
        $this->assertDatabaseMissing('employee_business_cards', [
            'employee_id' => $this->testEmployee->id,
        ]);

        // Accessing modal creates card record
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(200);

        $this->assertDatabaseHas('employee_business_cards', [
            'employee_id' => $this->testEmployee->id,
        ]);

        $card = EmployeeBusinessCard::where('employee_id', $this->testEmployee->id)->first();
        $token1 = $card->public_token;

        // Repeated access returns identical token
        $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $card->refresh();
        $this->assertSame($token1, $card->public_token);
        $this->assertSame(1, EmployeeBusinessCard::where('employee_id', $this->testEmployee->id)->count());
    }

    /**
     * Test user with only view permission cannot update card settings (403 Forbidden).
     */
    public function test_user_with_only_view_permission_cannot_update_settings(): void
    {
        $response = $this->actingAs($this->viewOnlyUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 1,
                'show_phone' => 1,
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test updating privacy flags saves correctly for authorized HRD.
     */
    public function test_hrd_can_update_privacy_flags(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 1,
                'show_phone' => 1,
                'show_email' => 1,
                'show_photo' => 1,
                'show_department' => 1,
                'show_location' => 0,
                'custom_title' => 'Senior Technical Lead',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_business_cards', [
            'employee_id' => $this->testEmployee->id,
            'is_active' => true,
            'show_phone' => true,
            'show_email' => true,
            'show_photo' => true,
            'show_department' => true,
            'show_location' => false,
            'custom_title' => 'Senior Technical Lead',
        ]);
    }

    /**
     * Test activation and deactivation works as expected.
     */
    public function test_activation_and_deactivation(): void
    {
        // 1. Activate
        $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 1,
            ]);

        $this->assertTrue(EmployeeBusinessCard::where('employee_id', $this->testEmployee->id)->value('is_active'));

        // 2. Deactivate
        $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 0,
            ]);

        $this->assertFalse(EmployeeBusinessCard::where('employee_id', $this->testEmployee->id)->value('is_active'));
    }

    /**
     * Test card cannot be activated for ineligible employee (e.g., expired contract).
     */
    public function test_ineligible_contract_employee_cannot_be_activated(): void
    {
        $contractEmployee = Employee::create([
            'nik' => 'EMP-EXP-001',
            'full_name' => 'Expired Contract Employee',
            'email' => 'expired@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Contract',
            'contract_start_date' => Carbon::now()->subMonths(6)->toDateString(),
            'contract_end_date' => Carbon::now()->subDays(5)->toDateString(), // Expired 5 days ago
            'spd_limit' => 5000000,
            'total_annual_leave' => 12,
        ]);

        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $contractEmployee), [
                'is_active' => 1,
            ]);

        $response->assertSessionHasErrors('is_active');
        $response->assertSessionHas('error');

        $this->assertFalse(
            EmployeeBusinessCard::where('employee_id', $contractEmployee->id)->value('is_active'),
            'Card must remain inactive when activation fails eligibility'
        );
    }

    /**
     * Test soft-deleted employee cannot be updated or activated.
     */
    public function test_soft_deleted_employee_cannot_be_activated(): void
    {
        $card = $this->service->getOrCreateCard($this->testEmployee);
        $this->assertFalse($card->is_active);

        $this->testEmployee->delete();
        $this->assertTrue($this->testEmployee->trashed());

        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 1,
            ]);

        // Route model binding automatically excludes soft-deleted models, returning 404 Not Found
        $response->assertNotFound();

        $card->refresh();
        $this->assertFalse($card->is_active, 'Card must remain inactive for soft-deleted employee');
    }

    /**
     * Test downloading QR Code PNG uses generic filename without NIK or employee ID.
     */
    public function test_download_qr_png_uses_generic_filename_without_nik_or_id(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.qr', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Cache-Control', 'no-cache, private');
        $response->assertHeader('Content-Disposition', 'attachment; filename="rapid-business-card-qr.png"');

        // Confirm NIK and ID are NOT exposed in Content-Disposition header
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringNotContainsString($this->testEmployee->nik, $disposition);
        $this->assertStringNotContainsString((string) $this->testEmployee->id, $disposition);

        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($content, 0, 8));
    }

    /**
     * Test QR payload remains unchanged and contains strictly the public URL.
     */
    public function test_qr_payload_contains_strictly_public_url(): void
    {
        $card = $this->service->getOrCreateCard($this->testEmployee);
        $expectedUrl = url('/c/' . $card->public_token);

        $this->assertSame($expectedUrl, $this->service->getPublicUrl($card));

        $svg = $this->service->getCardQrSvg($card);
        $this->assertNotEmpty($svg);
        $this->assertStringNotContainsString($this->testEmployee->nik, $svg);
    }

    /**
     * Test placeholder office address disables the show_location switch in modal.
     */
    public function test_placeholder_office_address_disables_show_location_switch_in_modal(): void
    {
        config(['company.office_address' => 'Jakarta, Indonesia']);
        $this->assertFalse($this->service->isOfficeAddressConfigured());

        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertSee('Alamat kantor pusat resmi belum dikonfigurasi.');
        $response->assertSee('id="show_location_switch"', false);
    }

    /**
     * Test direct request attempting to enable show_location with placeholder address is rejected.
     */
    public function test_direct_request_attempting_to_enable_show_location_with_placeholder_is_rejected(): void
    {
        config(['company.office_address' => 'Jakarta, Indonesia']);
        $card = $this->service->getOrCreateCard($this->testEmployee);
        $this->assertFalse($card->show_location);

        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'show_location' => 1,
            ]);

        $response->assertSessionHasErrors('show_location');
        $response->assertSessionHas('error');

        $card->refresh();
        $this->assertFalse($card->show_location, 'show_location must remain false when address is unconfigured');
    }

    /**
     * Test configured office address allows authorized user to enable show_location.
     */
    public function test_configured_office_address_allows_enabling_show_location(): void
    {
        config(['company.office_address' => 'Gedung Menara Mandiri Lt. 12, Jl. Jend. Sudirman Kav. 54-55, Jakarta Selatan']);
        $this->assertTrue($this->service->isOfficeAddressConfigured());

        $card = $this->service->getOrCreateCard($this->testEmployee);

        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $this->testEmployee), [
                'show_location' => 1,
            ]);

        $response->assertSessionHas('success');

        $card->refresh();
        $this->assertTrue($card->show_location);
    }

    /**
     * Test downloading QR Code PNG is forbidden for unauthorized roles.
     */
    public function test_download_qr_png_forbidden_for_site_user(): void
    {
        $response = $this->actingAs($this->siteUser)
            ->get(route('employees.business-card.qr', $this->testEmployee));

        $response->assertStatus(403);
    }

    /**
     * Test Employee Detail show view renders Digital Business Card button for authorized HRD.
     */
    public function test_employee_show_view_renders_digital_business_card_button(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.show', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertSee('Digital Business Card');
        $response->assertSee('data-bs-target="#digitalBusinessCardModal"', false);
        $response->assertSee('id="digitalBusinessCardModal"', false);
    }

    /**
     * Test permanent eligible employee modal renders activation switch enabled without warning.
     */
    public function test_permanent_eligible_employee_modal_renders_switch_enabled(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.show', $this->testEmployee));

        $response->assertStatus(200);
        $response->assertDontSee('Status Pegawai Tidak Memenuhi Syarat');
        $response->assertSee('id="is_active_switch"', false);
        $this->assertStringNotContainsString('id="is_active_switch" name="is_active" value="1"  disabled', $response->getContent());
        $response->assertSee('qr-preview-frame');
        $response->assertSee('qr-svg-container');
    }

    /**
     * Test contract employee with valid dates can be activated.
     */
    public function test_valid_contract_employee_can_be_activated(): void
    {
        $contractEmp = Employee::create([
            'nik' => 'EMP-CON-001',
            'full_name' => 'Valid Contract Employee',
            'email' => 'valid.contract@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Contract',
            'contract_start_date' => Carbon::now()->subMonths(1)->toDateString(),
            'contract_end_date' => Carbon::now()->addMonths(6)->toDateString(),
            'spd_limit' => 5000000,
            'total_annual_leave' => 12,
        ]);

        $this->assertTrue($this->service->isEligibleForPublic($contractEmp));

        $response = $this->actingAs($this->hrdUser)
            ->put(route('employees.business-card.update', $contractEmp), [
                'is_active' => 1,
            ]);

        $response->assertSessionHas('success');
        $this->assertTrue(EmployeeBusinessCard::where('employee_id', $contractEmp->id)->value('is_active'));
    }

    /**
     * Test AJAX update endpoint returns JSON with updated card.
     */
    public function test_ajax_update_returns_json_and_updates_card(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->putJson(route('employees.business-card.update', $this->testEmployee), [
                'is_active' => 1,
                'show_phone' => 1,
                'show_email' => 1,
                'custom_title' => 'Chief Technology Officer',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'card' => [
                'is_active' => true,
                'show_phone' => true,
                'show_email' => true,
                'custom_title' => 'Chief Technology Officer',
            ],
        ]);

        $card = EmployeeBusinessCard::where('employee_id', $this->testEmployee->id)->first();
        $this->assertTrue($card->is_active);
        $this->assertSame('Chief Technology Officer', $card->custom_title);
    }
}
