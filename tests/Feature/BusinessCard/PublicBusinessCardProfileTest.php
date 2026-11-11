<?php

namespace Tests\Feature\BusinessCard;

use App\Models\CostLevel;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeBusinessCard;
use App\Models\Position;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBusinessCardProfileTest extends TestCase
{
    use RefreshDatabase;

    protected DigitalBusinessCardService $service;
    protected Division $division;
    protected Position $position;
    protected CostLevel $costLevel;
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
            'description' => 'Civil Engineering Lead',
        ]);

        $this->costLevel = CostLevel::create([
            'name' => 'Manager',
            'is_active' => true,
        ]);

        $this->employee = Employee::create([
            'nik' => 'EMP-PUBLIC-999',
            'full_name' => 'Ir. Hendra Gunawan',
            'email' => 'hendra.gunawan@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'phone_number' => '081234567899',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Permanent',
            'spd_limit' => 20000000,
            'total_annual_leave' => 12,
            'photo' => 'employees/photos/test_profile_sample.jpg',
        ]);

        $this->card = $this->service->getOrCreateCard($this->employee);
        // Activate card for public tests
        $this->service->updateSettings($this->card, ['is_active' => true]);
    }

    /**
     * Test public profile accessible to guests with 200 OK.
     */
    public function test_public_profile_accessible_by_guests(): void
    {
        $response = $this->get(route('public.business-card.show', $this->card->public_token));

        $response->assertStatus(200);
        $response->assertSee('Ir. Hendra Gunawan');
        $response->assertSee('Principal Civil Engineer');
        $response->assertSee('PT Rapid Infrastruktur Indonesia');
        $response->assertDontSee('EIMS'); // No internal EIMS branding
    }

    /**
     * Test crawler and cache headers on public profile.
     */
    public function test_crawler_directives_and_cache_control_headers(): void
    {
        $response = $this->get(route('public.business-card.show', $this->card->public_token));

        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $response->assertSee('<meta name="robots" content="noindex, nofollow, noarchive">', false);
    }

    /**
     * Test unknown token returns 404.
     */
    public function test_unknown_token_returns_404(): void
    {
        $fakeToken = str_repeat('a', 64);
        $response = $this->get(route('public.business-card.show', $fakeToken));

        $response->assertNotFound();
    }

    /**
     * Test invalid token format (non-hex or wrong length) returns 404.
     */
    public function test_invalid_token_format_returns_404(): void
    {
        $response1 = $this->get('/c/short-token');
        $response1->assertNotFound();

        $response2 = $this->get('/c/' . str_repeat('z', 64)); // Non-hex
        $response2->assertNotFound();
    }

    /**
     * Test inactive card returns 404 without leaking info.
     */
    public function test_inactive_card_returns_404(): void
    {
        $this->service->updateSettings($this->card, ['is_active' => false]);

        $response = $this->get(route('public.business-card.show', $this->card->public_token));
        $response->assertNotFound();
    }

    /**
     * Test soft-deleted employee returns 404.
     */
    public function test_soft_deleted_employee_returns_404(): void
    {
        $this->employee->delete();
        $this->assertTrue($this->employee->trashed());

        $response = $this->get(route('public.business-card.show', $this->card->public_token));
        $response->assertNotFound();
    }

    /**
     * Test contract employee with expired contract returns 404.
     */
    public function test_expired_contract_employee_returns_404(): void
    {
        $contractEmp = Employee::create([
            'nik' => 'EMP-CTR-001',
            'full_name' => 'Contract Expired Guy',
            'email' => 'contract.expired@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Contract',
            'contract_start_date' => Carbon::now()->subMonths(3)->toDateString(),
            'contract_end_date' => Carbon::now()->subDays(2)->toDateString(),
            'spd_limit' => 5000000,
            'total_annual_leave' => 12,
        ]);

        $card = $this->service->getOrCreateCard($contractEmp);
        // Force is_active on model for test
        $card->is_active = true;
        $card->save();

        $response = $this->get(route('public.business-card.show', $card->public_token));
        $response->assertNotFound();
    }

    /**
     * Test individual privacy flags: show_phone.
     */
    public function test_privacy_flag_show_phone(): void
    {
        // 1. When false: Phone and WhatsApp actions are hidden
        $this->service->updateSettings($this->card, ['show_phone' => false]);
        $response1 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response1->assertDontSee($this->employee->phone_number);
        $response1->assertDontSee('https://wa.me/');

        // 2. When true: Phone and WhatsApp actions are rendered
        $this->service->updateSettings($this->card, ['show_phone' => true]);
        $response2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response2->assertSee($this->employee->phone_number);
        $response2->assertSee('https://wa.me/6281234567899');
        $response2->assertSee('tel:081234567899');
    }

    /**
     * Test individual privacy flags: show_email.
     */
    public function test_privacy_flag_show_email(): void
    {
        // 1. When false
        $this->service->updateSettings($this->card, ['show_email' => false]);
        $response1 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response1->assertDontSee('mailto:' . $this->employee->email);

        // 2. When true
        $this->service->updateSettings($this->card, ['show_email' => true]);
        $response2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response2->assertSee('mailto:' . $this->employee->email);
        $response2->assertSee($this->employee->email);
    }

    /**
     * Test individual privacy flags: show_photo.
     */
    public function test_privacy_flag_show_photo(): void
    {
        // 1. When false: Shows initials avatar placeholder
        $this->service->updateSettings($this->card, ['show_photo' => false]);
        $response1 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response1->assertDontSee('test_profile_sample.jpg');
        $response1->assertSee('avatar-placeholder');

        // 2. When true: Renders photo img tag
        $this->service->updateSettings($this->card, ['show_photo' => true]);
        $response2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response2->assertSee('test_profile_sample.jpg');
    }

    /**
     * Test individual privacy flags: show_department (omitted from public profile per design revision).
     */
    public function test_privacy_flag_show_department(): void
    {
        // 1. When false
        $this->service->updateSettings($this->card, ['show_department' => false]);
        $response1 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response1->assertDontSee('Engineering');

        // 2. When true (Division is strictly omitted from Public VCard profile per PO design directive)
        $this->service->updateSettings($this->card, ['show_department' => true]);
        $response2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response2->assertDontSee('Engineering');

        // 3. VCF still includes division when show_department is active
        $vcfResponse = $this->get(route('public.business-card.vcf', $this->card->public_token));
        $this->assertStringContainsString("ROLE:Engineering\r\n", $vcfResponse->getContent());
    }

    /**
     * Test office address display on public profile and VCF location privacy flag.
     */
    public function test_privacy_flag_show_location(): void
    {
        // 1. Public profile always displays approved official office address as company information
        $response = $this->get(route('public.business-card.show', $this->card->public_token));
        $response->assertSee('Kantor Pusat Resmi');
        $response->assertSee('Talavera Office Park');

        // 2. In VCF: only included when show_location is true
        $this->service->updateSettings($this->card, ['show_location' => false]);
        $vcf1 = $this->service->generateVcf($this->employee, $this->card);
        $this->assertStringNotContainsString('ADR;TYPE=WORK:', $vcf1);

        $this->service->updateSettings($this->card, ['show_location' => true]);
        $vcf2 = $this->service->generateVcf($this->employee, $this->card);
        $this->assertStringContainsString('ADR;TYPE=WORK:', $vcf2);
        $this->assertStringContainsString('Talavera Office Park', $vcf2);
    }

    /**
     * Test sensitive employee fields are never leaked in public HTML.
     */
    public function test_sensitive_employee_fields_are_never_leaked(): void
    {
        $response = $this->get(route('public.business-card.show', $this->card->public_token));

        $response->assertDontSee($this->employee->nik);
        $response->assertDontSee('20000000'); // spd_limit
        $response->assertDontSee('Islam'); // religion
        $response->assertDontSee('Male'); // gender
        $response->assertDontSee('birth_date');
        $response->assertDontSee('npwp');
        $response->assertDontSee('bpjs');
    }

    /**
     * Test VCF 3.0 download endpoint returns valid vCard stream.
     */
    public function test_vcf_download_returns_valid_vcard_stream(): void
    {
        $this->service->updateSettings($this->card, [
            'show_phone' => true,
            'show_email' => true,
            'show_department' => true,
        ]);

        $response = $this->get(route('public.business-card.vcf', $this->card->public_token));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/vcard; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="rapid-contact.vcf"');

        $content = $response->getContent();
        $this->assertStringStartsWith("BEGIN:VCARD\r\n", $content);
        $this->assertStringContainsString("VERSION:3.0\r\n", $content);
        $this->assertStringContainsString("FN:Ir. Hendra Gunawan\r\n", $content);
        $this->assertStringContainsString("ORG:PT Rapid Infrastruktur Indonesia\r\n", $content);
        $this->assertStringContainsString("TITLE:Principal Civil Engineer\r\n", $content);
        $this->assertStringContainsString("ROLE:Engineering\r\n", $content);
        $this->assertStringContainsString("TEL;TYPE=CELL,VOICE:081234567899\r\n", $content);
        $this->assertStringContainsString("EMAIL;TYPE=INTERNET,WORK:hendra.gunawan@rapidinfrastruktur.com\r\n", $content);
        $this->assertStringEndsWith("END:VCARD\r\n", $content);

        // Never leak NIK or ID in VCF
        $this->assertStringNotContainsString($this->employee->nik, $content);
    }

    /**
     * Test VCF CRLF injection prevention and special character sanitization.
     */
    public function test_vcf_crlf_injection_prevention(): void
    {
        $this->employee->full_name = "Hacker Name\r\nINJECTED:VALUE\r\n";
        $this->employee->save();

        $vcf = $this->service->generateVcf($this->employee, $this->card);

        // Confirm CRLF was stripped so no rogue line was injected
        $this->assertStringNotContainsString("FN:Hacker Name\r\nINJECTED", $vcf);
        $this->assertStringContainsString("FN:Hacker Name  INJECTED:VALUE", $vcf);
    }

    /**
     * Test VCF does not contain private fields when flags are false.
     */
    public function test_vcf_does_not_contain_private_fields_when_flags_are_false(): void
    {
        $this->service->updateSettings($this->card, [
            'show_phone' => false,
            'show_email' => false,
            'show_department' => false,
            'show_location' => false,
        ]);

        $vcf = $this->service->generateVcf($this->employee, $this->card);

        $this->assertStringNotContainsString('TEL;', $vcf);
        $this->assertStringNotContainsString('EMAIL;', $vcf);
        $this->assertStringNotContainsString('ROLE;', $vcf);
        $this->assertStringNotContainsString('ADR;', $vcf);
    }

    /**
     * Test viewing public profile increments scan count.
     */
    public function test_viewing_public_profile_increments_scan_count(): void
    {
        $this->assertSame(0, $this->card->scan_count);
        $this->assertNull($this->card->last_scanned_at);

        $this->get(route('public.business-card.show', $this->card->public_token));

        $this->card->refresh();
        $this->assertSame(1, $this->card->scan_count);
        $this->assertNotNull($this->card->last_scanned_at);
    }

    /**
     * Test public visits do not rotate token.
     */
    public function test_public_visits_do_not_rotate_token(): void
    {
        $originalToken = $this->card->public_token;

        $this->get(route('public.business-card.show', $this->card->public_token));
        $this->get(route('public.business-card.vcf', $this->card->public_token));

        $this->card->refresh();
        $this->assertSame($originalToken, $this->card->public_token);
    }

    /**
     * Test company website is suppressed when unconfigured or placeholder.
     */
    public function test_company_website_placeholder_suppression(): void
    {
        // 1. Explicit placeholder ('https://example.com')
        config(['company.website' => 'https://example.com']);
        $this->assertFalse($this->service->isCompanyWebsiteConfigured());

        $vcf = $this->service->generateVcf($this->employee, $this->card);
        $this->assertStringNotContainsString('URL:https://example.com', $vcf);

        // 2. Verified official website ('https://www.rapidinfrastruktur.com')
        config(['company.website' => 'https://www.rapidinfrastruktur.com']);
        $this->assertTrue($this->service->isCompanyWebsiteConfigured());

        $responseHtml2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $responseHtml2->assertSee('Situs Web Resmi');
        $responseHtml2->assertSee('https://www.rapidinfrastruktur.com');

        $vcf2 = $this->service->generateVcf($this->employee, $this->card);
        $this->assertStringContainsString("URL:https://www.rapidinfrastruktur.com\r\n", $vcf2);
    }

    /**
     * Test WhatsApp direct button and phone number normalization.
     */
    public function test_whatsapp_button_and_normalization(): void
    {
        // 1. WhatsApp button present when show_phone is true
        $this->service->updateSettings($this->card, ['show_phone' => true]);
        $response = $this->get(route('public.business-card.show', $this->card->public_token));

        $response->assertSee('Chat WhatsApp');
        $response->assertSee('https://wa.me/6281234567899');
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);

        // 2. WhatsApp button absent when show_phone is false
        $this->service->updateSettings($this->card, ['show_phone' => false]);
        $response2 = $this->get(route('public.business-card.show', $this->card->public_token));
        $response2->assertDontSee('Chat WhatsApp');
        $response2->assertDontSee('https://wa.me/6281234567899');

        // 3. Normalization logic
        $this->assertSame('https://wa.me/6281234567890', $this->service->formatWhatsAppUrl('081234567890'));
        $this->assertSame('https://wa.me/6281234567890', $this->service->formatWhatsAppUrl('+6281234567890'));
        $this->assertSame('https://wa.me/6281234567890', $this->service->formatWhatsAppUrl('6281234567890'));
        $this->assertSame('https://wa.me/6281234567890', $this->service->formatWhatsAppUrl('+62 812-3456-7890'));
        $this->assertSame('https://wa.me/6281234567890', $this->service->formatWhatsAppUrl('62081234567890'));
        $this->assertNull($this->service->formatWhatsAppUrl(''));
        $this->assertNull($this->service->formatWhatsAppUrl(null));
        $this->assertNull($this->service->formatWhatsAppUrl('123')); // too short
    }
}
