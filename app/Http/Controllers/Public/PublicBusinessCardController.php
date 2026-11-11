<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EmployeeBusinessCard;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Illuminate\Http\Request;

class PublicBusinessCardController extends Controller
{
    public function __construct(
        protected DigitalBusinessCardService $cardService
    ) {}

    /**
     * Resolve and validate card & employee for public access.
     * Enforces fail-closed uniform 404 for any ineligible, inactive, or invalid token.
     */
    protected function resolveCardAndEmployee(string $token): array
    {
        // 1. Validate token format (strictly 64 hex characters)
        if (strlen($token) !== 64 || ! ctype_xdigit($token)) {
            abort(404);
        }

        // 2. Query card
        $card = EmployeeBusinessCard::where('public_token', $token)->first();

        if (! $card || ! $card->is_active) {
            abort(404);
        }

        // 3. Load employee
        $employee = $card->employee;

        if (! $employee) {
            abort(404);
        }

        // 4. Strict fail-closed eligibility check
        if (! $this->cardService->isEligibleForPublic($employee, $card)) {
            abort(404);
        }

        return [$card, $employee];
    }

    /**
     * Display public Digital Business Card profile.
     */
    public function show(string $token)
    {
        [$card, $employee] = $this->resolveCardAndEmployee($token);

        // Record scan event
        $this->cardService->recordScan($card);

        $whatsappUrl = $card->show_phone
            ? $this->cardService->formatWhatsAppUrl($employee->phone_number)
            : null;

        $isAddressConfigured = $this->cardService->isOfficeAddressConfigured();
        $isWebsiteConfigured = $this->cardService->isCompanyWebsiteConfigured();

        return response()
            ->view('public.business_card', compact(
                'card',
                'employee',
                'whatsappUrl',
                'isAddressConfigured',
                'isWebsiteConfigured'
            ))
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->header('Cache-Control', 'no-cache, private, no-store, must-revalidate');
    }

    /**
     * Download VCF 3.0 contact file.
     */
    public function downloadVcf(string $token)
    {
        [$card, $employee] = $this->resolveCardAndEmployee($token);

        $vcfContent = $this->cardService->generateVcf($employee, $card);

        // Generic filename without NIK or employee ID
        $filename = 'rapid-contact.vcf';

        return response($vcfContent, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, private, no-store, must-revalidate',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ]);
    }
}
