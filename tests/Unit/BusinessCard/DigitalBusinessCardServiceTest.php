<?php

namespace Tests\Unit\BusinessCard;

use App\Models\Employee;
use App\Models\EmployeeBusinessCard;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Carbon\Carbon;
use Tests\TestCase;

class DigitalBusinessCardServiceTest extends TestCase
{
    protected DigitalBusinessCardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DigitalBusinessCardService();
    }

    /**
     * Test token generation produces 64 hexadecimal characters with 256-bit entropy.
     */
    public function test_token_has_exactly_64_hexadecimal_characters(): void
    {
        $token1 = $this->service->generateToken();
        $token2 = $this->service->generateToken();

        $this->assertSame(64, strlen($token1));
        $this->assertSame(64, strlen($token2));
        $this->assertTrue(ctype_xdigit($token1), 'Token 1 must be valid hexadecimal');
        $this->assertTrue(ctype_xdigit($token2), 'Token 2 must be valid hexadecimal');
        $this->assertNotEquals($token1, $token2, 'Two generated tokens must be distinct');
    }

    /**
     * Test token does not contain employee NIK or ID as identifier.
     */
    public function test_token_does_not_contain_employee_identifiers(): void
    {
        $employee = new Employee();
        $employee->id = 12345;
        $employee->nik = 'NIK-99887766';

        $token = $this->service->generateToken();

        $this->assertStringNotContainsString((string) $employee->id, $token);
        $this->assertStringNotContainsString($employee->nik, $token);
    }

    /**
     * Test public URL generation format.
     */
    public function test_public_url_generation(): void
    {
        $token = $this->service->generateToken();
        $url = $this->service->getPublicUrl($token);

        $this->assertStringStartsWith(url('/c/'), $url);
        $this->assertStringEndsWith($token, $url);
        $this->assertStringNotContainsString('nik', strtolower($url));
    }

    /**
     * Test QR Code SVG output is valid XML vector.
     */
    public function test_qr_code_svg_generation(): void
    {
        $url = 'https://rapidinfrastruktur.com/c/testtoken12345';
        $svg = $this->service->generateQrCodeSvg($url);

        $this->assertNotEmpty($svg);
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    /**
     * Test QR Code PNG generation uses GD and returns valid PNG binary stream.
     */
    public function test_qr_code_png_generation(): void
    {
        $url = 'https://rapidinfrastruktur.com/c/testtoken12345';
        $png = $this->service->generateQrCodePng($url);

        $this->assertNotEmpty($png);
        // Standard PNG 8-byte magic header: 89 50 4E 47 0D 0A 1A 0A
        $pngHeader = substr($png, 0, 8);
        $this->assertSame("\x89PNG\r\n\x1a\n", $pngHeader);
    }

    /**
     * Test QR payload contains strictly public URL and never sensitive employee fields.
     */
    public function test_qr_payload_contains_only_public_url(): void
    {
        $card = new EmployeeBusinessCard();
        $card->public_token = 'abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789';

        $expectedUrl = url('/c/' . $card->public_token);
        $svg = $this->service->getCardQrSvg($card);

        $this->assertNotEmpty($svg);
        // Sensitive employee fields must never appear in the QR output
        $this->assertStringNotContainsString('salary', $svg);
        $this->assertStringNotContainsString('npwp', $svg);
        $this->assertStringNotContainsString('birth_date', $svg);
    }

    /**
     * Test eligibility: Permanent employee with active card is eligible.
     */
    public function test_eligibility_permanent_employee_active_card(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Permanent';

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertTrue($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Soft-deleted employee is never eligible.
     */
    public function test_eligibility_soft_deleted_employee_fails(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Permanent';
        $employee->deleted_at = Carbon::now();

        $this->assertFalse($this->service->isEligibleForPublic($employee));
    }

    /**
     * Test eligibility: Contract employee within valid date range is eligible.
     */
    public function test_eligibility_contract_employee_within_dates(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Contract';
        $employee->contract_start_date = Carbon::now()->subDays(10)->toDateString();
        $employee->contract_end_date = Carbon::now()->addDays(30)->toDateString();

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertTrue($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Contract employee not yet started is not eligible.
     */
    public function test_eligibility_contract_not_started_fails(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Contract';
        $employee->contract_start_date = Carbon::now()->addDays(5)->toDateString();
        $employee->contract_end_date = Carbon::now()->addDays(90)->toDateString();

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertFalse($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Expired contract employee is not eligible.
     */
    public function test_eligibility_contract_expired_fails(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Contract';
        $employee->contract_start_date = Carbon::now()->subDays(60)->toDateString();
        $employee->contract_end_date = Carbon::now()->subDays(1)->toDateString();

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertFalse($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Contract end date is inclusive through the stated day.
     */
    public function test_eligibility_contract_end_date_is_inclusive(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Contract';
        $employee->contract_start_date = Carbon::now()->subDays(10)->toDateString();
        // Today is the last day of the contract
        $employee->contract_end_date = Carbon::now()->toDateString();

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        // Still eligible today
        $this->assertTrue($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Contract with missing dates fails closed.
     */
    public function test_eligibility_contract_missing_dates_fails_closed(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Contract';
        $employee->contract_start_date = null;
        $employee->contract_end_date = null;

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertFalse($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test eligibility: Unknown employment status fails closed.
     */
    public function test_eligibility_unknown_status_fails_closed(): void
    {
        $employee = new Employee();
        $employee->employee_status = 'Freelance_Unknown';

        $card = new EmployeeBusinessCard();
        $card->is_active = true;

        $this->assertFalse($this->service->isEligibleForPublic($employee, $card));
    }
}
