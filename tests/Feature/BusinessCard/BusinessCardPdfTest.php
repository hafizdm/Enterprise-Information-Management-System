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

class BusinessCardPdfTest extends TestCase
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

    protected Employee $employee;
    protected EmployeeBusinessCard $card;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->service = new DigitalBusinessCardService();

        $this->division = Division::create([
            'name' => 'Engineering',
            'code' => 'ENG',
            'is_active' => true,
        ]);

        $this->position = Position::create([
            'name' => 'Principal Civil Engineer',
            'division_id' => $this->division->id,
            'description' => 'Principal Engineer',
        ]);

        $this->costLevel = CostLevel::create([
            'name' => 'Manager',
            'is_active' => true,
        ]);

        $this->employee = Employee::create([
            'nik' => 'HO20261101',
            'full_name' => 'Ir. Hendra Gunawan',
            'email' => 'hendra.gunawan@rapidinfrastruktur.com',
            'phone_number' => '081234567899',
            'gender' => 'Male',
            'religion' => 'Islam',
            'employee_status' => 'Permanent',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'join_date' => Carbon::now()->subYears(2),
            'spd_limit' => 20000000,
        ]);

        $this->card = $this->service->getOrCreateCard($this->employee);

        // System Administrator
        $this->adminUser = User::create([
            'username' => 'admin.pdf',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('System Administrator');

        // HRD
        $this->hrdUser = User::create([
            'username' => 'hrd.pdf',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // HR/GA Site (No employee.view-any)
        $this->siteUser = User::create([
            'username' => 'site.pdf',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->siteUser->assignRole('HR/GA Site');

        // Ordinary Employee
        $this->regularEmployeeUser = User::create([
            'username' => 'emp.pdf',
            'password' => bcrypt('password'),
            'employee_id' => $this->employee->id,
            'is_active' => true,
        ]);
        $this->regularEmployeeUser->assignRole('Employee');
    }

    /**
     * Test guest cannot access PDF endpoint.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('employees.business-card.pdf', $this->employee));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test ordinary employee without employee.view-any receives 403.
     */
    public function test_ordinary_employee_cannot_access_pdf(): void
    {
        $response = $this->actingAs($this->regularEmployeeUser)
            ->get(route('employees.business-card.pdf', $this->employee));

        $response->assertForbidden();
    }

    /**
     * Test HR/GA Site without employee.view-any receives 403.
     */
    public function test_site_user_cannot_access_pdf(): void
    {
        $response = $this->actingAs($this->siteUser)
            ->get(route('employees.business-card.pdf', $this->employee));

        $response->assertForbidden();
    }

    /**
     * Test HRD can access PDF endpoint with 200 OK and PDF mime type.
     */
    public function test_hrd_can_access_and_generate_pdf(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.pdf', $this->employee));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('filename=rapid-business-card.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    /**
     * Test System Administrator can access PDF endpoint.
     */
    public function test_admin_can_access_pdf(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('employees.business-card.pdf', $this->employee));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test PDF download parameter sets attachment Content-Disposition.
     */
    public function test_pdf_download_parameter(): void
    {
        $response = $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.pdf', ['employee' => $this->employee, 'download' => 1]));

        $response->assertOk();
        $this->assertStringStartsWith('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('filename=rapid-business-card.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    /**
     * Test PDF physical dimensions are exactly 85mm x 55mm with 2 sides (Front & Back).
     */
    public function test_pdf_dimensions_are_exact_85x55_mm_and_two_sided(): void
    {
        $pdf = $this->service->generatePdf($this->employee, $this->card);
        $output = $pdf->output();

        // 1. Check exact MediaBox: 85mm = 240.945 pt, 55mm = 155.906 pt
        preg_match_all('/\/MediaBox\s*\[\s*([\d\.]+)\s+([\d\.]+)\s+([\d\.]+)\s+([\d\.]+)\s*\]/', $output, $matches);
        $this->assertNotEmpty($matches[0], 'MediaBox not found in PDF output.');
        $widthPt = floatval($matches[3][0]);
        $heightPt = floatval($matches[4][0]);

        $this->assertEqualsWithDelta(240.945, $widthPt, 0.5, 'Width must be 85mm (approx 240.95 pt)');
        $this->assertEqualsWithDelta(155.906, $heightPt, 0.5, 'Height must be 55mm (approx 155.91 pt)');

        // 2. Exactly 2 pages (Front side and Back side)
        preg_match_all('/\/Type\s*\/Page\b/', $output, $pageMatches);
        $this->assertCount(2, $pageMatches[0], 'Business card must strictly have 2 pages (front and back sides).');
    }

    /**
     * Test token idempotency: Generating PDF does not mutate public_token.
     */
    public function test_generating_pdf_does_not_mutate_token(): void
    {
        $initialToken = $this->card->public_token;

        $this->actingAs($this->hrdUser)
            ->get(route('employees.business-card.pdf', $this->employee));

        $this->card->refresh();
        $this->assertSame($initialToken, $this->card->public_token);
    }

    /**
     * Test sensitive employee fields are never leaked into PDF.
     */
    public function test_sensitive_employee_fields_never_leaked_in_pdf(): void
    {
        $pdf = $this->service->generatePdf($this->employee, $this->card);
        $output = $pdf->output();

        // NIK, religion, spd_limit, gender
        $this->assertStringNotContainsString($this->employee->nik, $output);
        $this->assertStringNotContainsString('20000000', $output);
        $this->assertStringNotContainsString('Islam', $output);
    }

    /**
     * Test privacy flags behavior on PDF.
     */
    public function test_privacy_flags_respected_in_pdf(): void
    {
        // 1. All flags false (default)
        $this->service->updateSettings($this->card, [
            'show_phone' => false,
            'show_email' => false,
            'show_department' => false,
            'show_location' => false,
            'show_photo' => false,
        ]);

        $viewHtmlFalse = view('hr.employees.business_card_pdf', [
            'employee' => $this->employee,
            'card' => $this->card,
            'qrPngBase64' => base64_encode($this->service->getCardQrPng($this->card)),
            'logoBase64' => null,
            'photoBase64' => null,
            'isAddressConfigured' => false,
            'isWebsiteConfigured' => false,
        ])->render();

        $this->assertStringContainsString('Ir. Hendra Gunawan', $viewHtmlFalse);
        $this->assertStringContainsString('Principal Civil Engineer', $viewHtmlFalse);
        $this->assertStringNotContainsString('081234567899', $viewHtmlFalse);
        $this->assertStringNotContainsString('hendra.gunawan@rapidinfrastruktur.com', $viewHtmlFalse);
        $this->assertStringNotContainsString('Engineering', $viewHtmlFalse);

        // 2. Flags true
        $this->service->updateSettings($this->card, [
            'show_phone' => true,
            'show_email' => true,
            'show_department' => true,
        ]);

        $viewHtmlTrue = view('hr.employees.business_card_pdf', [
            'employee' => $this->employee,
            'card' => $this->card,
            'qrPngBase64' => base64_encode($this->service->getCardQrPng($this->card)),
            'logoBase64' => null,
            'photoBase64' => null,
            'isAddressConfigured' => true,
            'isWebsiteConfigured' => true,
        ])->render();

        $this->assertStringContainsString('081234567899', $viewHtmlTrue);
        $this->assertStringContainsString('hendra.gunawan@rapidinfrastruktur.com', $viewHtmlTrue);
        // Per official RAPID business card design revision, Division is never shown in print PDF
        $this->assertStringNotContainsString('Engineering', $viewHtmlTrue);
    }

    /**
     * Test custom title override in PDF.
     */
    public function test_custom_title_override_in_pdf(): void
    {
        $this->service->updateSettings($this->card, [
            'custom_title' => 'Chief Technology Officer',
        ]);

        $viewHtml = view('hr.employees.business_card_pdf', [
            'employee' => $this->employee,
            'card' => $this->card,
            'qrPngBase64' => base64_encode($this->service->getCardQrPng($this->card)),
            'logoBase64' => null,
            'photoBase64' => null,
            'isAddressConfigured' => false,
            'isWebsiteConfigured' => false,
        ])->render();

        $this->assertStringContainsString('Chief Technology Officer', $viewHtml);
    }
}
