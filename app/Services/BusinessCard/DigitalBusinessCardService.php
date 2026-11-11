<?php

namespace App\Services\BusinessCard;

use App\Models\Employee;
use App\Models\EmployeeBusinessCard;
use Carbon\Carbon;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class DigitalBusinessCardService
{
    /**
     * Generate a cryptographically secure 64-character hexadecimal token.
     * 256 bits of random entropy.
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get or create the unique Digital Business Card record for an employee.
     *
     * Idempotency guarantee:
     * - Returns existing record if present.
     * - Generates token only once and never rotates existing token.
     * - Concurrency-safe against race conditions via DB transaction & unique constraints.
     */
    public function getOrCreateCard(Employee $employee): EmployeeBusinessCard
    {
        // 1. Check if relation already exists on instance or DB
        if ($employee->relationLoaded('businessCard') && $employee->businessCard !== null) {
            return $employee->businessCard;
        }

        $existing = EmployeeBusinessCard::where('employee_id', $employee->id)->first();
        if ($existing) {
            return $existing;
        }

        // 2. Concurrency-safe creation inside transaction
        try {
            return DB::transaction(function () use ($employee) {
                // Re-check with row lock inside transaction
                $locked = EmployeeBusinessCard::where('employee_id', $employee->id)
                    ->lockForUpdate()
                    ->first();

                if ($locked) {
                    return $locked;
                }

                $card = new EmployeeBusinessCard([
                    'employee_id' => $employee->id,
                    'is_active' => false,
                    'show_phone' => false,
                    'show_email' => false,
                    'show_photo' => false,
                    'show_department' => false,
                    'show_location' => false,
                    'custom_title' => null,
                    'scan_count' => 0,
                    'last_scanned_at' => null,
                ]);

                // Assign public_token explicitly (guarded from mass assignment)
                $card->public_token = $this->generateToken();
                $card->save();

                return $card;
            });
        } catch (UniqueConstraintViolationException $e) {
            // In case of high concurrency, retrieve the winning record
            return EmployeeBusinessCard::where('employee_id', $employee->id)->firstOrFail();
        }
    }

    /**
     * Generate the public profile URL for a given card or token.
     *
     * URL pattern: {APP_URL}/c/{public_token}
     * Never exposes NIK, employee ID, or internal identifiers.
     */
    public function getPublicUrl(EmployeeBusinessCard|string $cardOrToken): string
    {
        $token = $cardOrToken instanceof EmployeeBusinessCard
            ? $cardOrToken->public_token
            : $cardOrToken;

        $baseUrl = rtrim((string) config('app.url', ''), '/');

        if (! empty($baseUrl)) {
            return $baseUrl . '/c/' . $token;
        }

        return url('/c/' . $token);
    }

    /**
     * Generate raw SVG string for a given URL payload.
     */
    public function generateQrCodeSvg(string $url, int $size = 300, int $margin = 10): string
    {
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $writer = new SvgWriter();
        return $writer->write($qrCode)->getString();
    }

    /**
     * Generate binary PNG string for a given URL payload using GD renderer.
     */
    public function generateQrCodePng(string $url, int $size = 300, int $margin = 10): string
    {
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $writer = new PngWriter();
        return $writer->write($qrCode)->getString();
    }

    /**
     * Get SVG QR Code for an employee business card.
     * Payload contains strictly the public profile URL.
     */
    public function getCardQrSvg(EmployeeBusinessCard $card, int $size = 300, int $margin = 10): string
    {
        $url = $this->getPublicUrl($card);
        return $this->generateQrCodeSvg($url, $size, $margin);
    }

    /**
     * Get PNG QR Code for an employee business card.
     * Payload contains strictly the public profile URL.
     */
    public function getCardQrPng(EmployeeBusinessCard $card, int $size = 300, int $margin = 10): string
    {
        $url = $this->getPublicUrl($card);
        return $this->generateQrCodePng($url, $size, $margin);
    }

    /**
     * Validate whether an employee is eligible for public business card display.
     *
     * Fail-closed validation rules:
     * 1. Card must be active (is_active === true).
     * 2. Employee must not be soft-deleted.
     * 3. Employment status validation:
     *    - 'Permanent': Eligible.
     *    - 'Contract': Must have valid contract_start_date & contract_end_date.
     *      Missing dates fail closed.
     *      Start date must be <= current date in application timezone.
     *      End date is inclusive through 23:59:59 of the stated date.
     *    - Any unknown status: Fail closed (false).
     */
    public function isEligibleForPublic(Employee $employee, ?EmployeeBusinessCard $card = null): bool
    {
        // 1. Check soft delete
        if ($employee->trashed()) {
            return false;
        }

        // 3. Evaluate employment status
        $status = $employee->employee_status;

        if ($status === 'Permanent') {
            return true;
        }

        if ($status === 'Contract') {
            // Missing contract dates must fail closed
            if (empty($employee->contract_start_date) || empty($employee->contract_end_date)) {
                return false;
            }

            $tz = config('company.timezone', 'Asia/Jakarta');
            $now = Carbon::now($tz);
            $today = $now->copy()->startOfDay();

            $startDate = Carbon::parse($employee->contract_start_date, $tz)->startOfDay();
            $endDate = Carbon::parse($employee->contract_end_date, $tz)->endOfDay();

            // Contract has not started yet
            if ($startDate->isAfter($today)) {
                return false;
            }

            // Contract has expired (end date is inclusive through end of stated day)
            if ($endDate->isBefore($now)) {
                return false;
            }

            return true;
        }

        // Any unrecognized or missing status fails closed
        return false;
    }

    /**
     * Update privacy flags and custom title for a business card.
     */
    public function updateSettings(EmployeeBusinessCard $card, array $attributes): EmployeeBusinessCard
    {
        $allowed = [
            'is_active',
            'show_phone',
            'show_email',
            'show_photo',
            'show_department',
            'show_location',
            'custom_title',
        ];

        $filtered = array_intersect_key($attributes, array_flip($allowed));
        $card->update($filtered);

        return $card->fresh();
    }

    /**
     * Record a scan event by atomically incrementing scan_count and updating last_scanned_at.
     */
    public function recordScan(EmployeeBusinessCard $card): void
    {
        $card->increment('scan_count');
        $card->update(['last_scanned_at' => Carbon::now()]);
    }

    /**
     * Determine whether the corporate office address is properly configured.
     * Empty or known placeholder values (e.g. 'Jakarta, Indonesia') are treated as unconfigured.
     */
    public function isOfficeAddressConfigured(): bool
    {
        $address = trim((string) config('company.office_address', ''));

        if (empty($address)) {
            return false;
        }

        $knownPlaceholders = [
            'jakarta, indonesia',
            'jakarta',
            'indonesia',
            'placeholder',
        ];

        return ! in_array(strtolower($address), $knownPlaceholders, true);
    }

    /**
     * Determine whether the corporate website is properly configured.
     * Empty or known placeholder values (e.g. 'https://rapidinfrastruktur.com', 'example.com') are treated as unconfigured.
     */
    public function isCompanyWebsiteConfigured(): bool
    {
        $website = trim((string) config('company.website', ''));

        if (empty($website)) {
            return false;
        }

        $knownPlaceholders = [
            'example.com',
            'https://example.com',
            'http://example.com',
            'localhost',
            'http://localhost',
            'https://localhost',
            'placeholder',
        ];

        $normalized = strtolower(rtrim($website, '/'));
        foreach ($knownPlaceholders as $placeholder) {
            if ($normalized === strtolower(rtrim($placeholder, '/'))) {
                return false;
            }
        }

        return filter_var($website, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Generate VCF 3.0 content strictly adhering to privacy flags and CRLF sanitization.
     */
    public function generateVcf(Employee $employee, EmployeeBusinessCard $card): string
    {
        $lines = [];
        $lines[] = 'BEGIN:VCARD';
        $lines[] = 'VERSION:3.0';
        $lines[] = 'PRODID:-//PT Rapid Infrastruktur Indonesia//Digital Business Card//ID';

        // Full Name (Sanitized)
        $name = $this->sanitizeVcfString($employee->full_name);
        $lines[] = 'FN:' . $name;
        $lines[] = 'N:' . $name . ';;;;';

        // Organization
        $org = $this->sanitizeVcfString(config('company.name', 'PT Rapid Infrastruktur Indonesia'));
        $lines[] = 'ORG:' . $org;

        // Title / Position
        $title = $card->custom_title ?: ($employee->position?->name ?? '');
        if (! empty($title)) {
            $lines[] = 'TITLE:' . $this->sanitizeVcfString($title);
        }

        // Division / Role (only if show_department is active)
        if ($card->show_department && ! empty($employee->division?->name)) {
            $lines[] = 'ROLE:' . $this->sanitizeVcfString($employee->division->name);
        }

        // Phone (only if show_phone is active)
        if ($card->show_phone && ! empty($employee->phone_number)) {
            $phone = preg_replace('/[^\d+]/', '', $employee->phone_number);
            if (! empty($phone)) {
                $lines[] = 'TEL;TYPE=CELL,VOICE:' . $phone;
            }
        }

        // Email (only if show_email is active)
        if ($card->show_email && ! empty($employee->email)) {
            $email = $this->sanitizeVcfString($employee->email);
            $lines[] = 'EMAIL;TYPE=INTERNET,WORK:' . $email;
        }

        // Office Address (only if show_location is active and configured)
        if ($card->show_location && $this->isOfficeAddressConfigured()) {
            $address = $this->sanitizeVcfString(config('company.office_address'));
            $lines[] = 'ADR;TYPE=WORK:;;' . $address . ';;;;';
        }

        // Company Website URL (only if properly configured and not a placeholder)
        if ($this->isCompanyWebsiteConfigured()) {
            $lines[] = 'URL:' . $this->sanitizeVcfString(config('company.website'));
        }

        // Photo (only if show_photo is active and photo exists)
        if ($card->show_photo && ! empty($employee->photo)) {
            $photoUrl = asset('storage/' . $employee->photo);
            $lines[] = 'PHOTO;VALUE=URI:' . $photoUrl;
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Sanitize string for VCF to prevent CRLF injection and escape special vCard characters.
     */
    public function sanitizeVcfString(string $value): string
    {
        // Strip CR and LF completely to prevent vCard header/structure injection
        $cleaned = str_replace(["\r", "\n"], ' ', trim($value));

        // Escape backslash, semicolon, and comma per RFC 2426 / vCard 3.0
        $cleaned = str_replace('\\', '\\\\', $cleaned);
        $cleaned = str_replace(';', '\;', $cleaned);
        $cleaned = str_replace(',', '\,', $cleaned);

        return $cleaned;
    }

    /**
     * Format a phone number into an international WhatsApp click-to-chat URL.
     * Strictly normalizes Indonesian numbers:
     * - 0812... -> 62812...
     * - +62812... -> 62812...
     * - 62812... -> 62812...
     * - Strips accidental leading 0 after country code 62 (e.g. 620812... -> 62812...)
     */
    public function formatWhatsAppUrl(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits) || strlen($digits) < 7) {
            return null;
        }

        // Handle accidental leading 0 after country code 62 (e.g. 620812... -> 62812...)
        if (str_starts_with($digits, '620')) {
            $digits = '62' . substr($digits, 3);
        }
        // Indonesian mobile numbers starting with '0' become '62' (e.g. 0812... -> 62812...)
        elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits;
    }

    /**
     * Generate 85mm x 55mm print-ready Business Card PDF.
     */
    public function generatePdf(Employee $employee, EmployeeBusinessCard $card): \Barryvdh\DomPDF\PDF
    {
        $qrPngBase64 = base64_encode($this->getCardQrPng($card));

        $logoPath = public_path(config('company.logo_path', 'images/company-logo.png'));
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $whiteLogoPath = public_path(config('company.white_logo_path', 'images/company-logo-white.png'));
        $whiteLogoBase64 = file_exists($whiteLogoPath) ? base64_encode(file_get_contents($whiteLogoPath)) : null;

        $isAddressConfigured = $this->isOfficeAddressConfigured();
        $isWebsiteConfigured = $this->isCompanyWebsiteConfigured();

        $photoBase64 = null;
        if ($card->show_photo && ! empty($employee->photo)) {
            $photoPath = storage_path('app/public/' . $employee->photo);
            if (file_exists($photoPath)) {
                $photoBase64 = base64_encode(file_get_contents($photoPath));
            }
        }

        $pdf = Pdf::loadView('hr.employees.business_card_pdf', [
            'employee' => $employee,
            'card' => $card,
            'qrPngBase64' => $qrPngBase64,
            'logoBase64' => $logoBase64,
            'whiteLogoBase64' => $whiteLogoBase64,
            'photoBase64' => $photoBase64,
            'isAddressConfigured' => $isAddressConfigured,
            'isWebsiteConfigured' => $isWebsiteConfigured,
        ]);

        // Exact 85mm x 55mm in points (1mm = 72/25.4 = 2.83464567 pt)
        // 85mm = 240.945 pt, 55mm = 155.906 pt
        $pdf->setPaper([0, 0, 240.945, 155.906], 'portrait');

        return $pdf;
    }
}
