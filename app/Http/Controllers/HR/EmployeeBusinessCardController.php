<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeBusinessCardController extends Controller
{
    public function __construct(
        protected DigitalBusinessCardService $cardService
    ) {}

    /**
     * Display the digital business card data / modal view.
     */
    public function show(Employee $employee)
    {
        Gate::authorize('view', $employee);

        $card = $this->cardService->getOrCreateCard($employee);
        $qrSvg = $this->cardService->getCardQrSvg($card);
        $publicUrl = $this->cardService->getPublicUrl($card);
        $isEligible = $this->cardService->isEligibleForPublic($employee);

        if (request()->wantsJson()) {
            return response()->json([
                'employee' => [
                    'id' => $employee->id,
                    'full_name' => $employee->full_name,
                    'nik' => $employee->nik,
                    'position' => $employee->position?->name,
                    'division' => $employee->division?->name,
                ],
                'card' => $card,
                'qr_svg' => $qrSvg,
                'public_url' => $publicUrl,
                'is_eligible' => $isEligible,
            ]);
        }

        return view('hr.employees.partials.business_card_modal', compact(
            'employee',
            'card',
            'qrSvg',
            'publicUrl',
            'isEligible'
        ));
    }

    /**
     * Update the digital business card settings (activation & privacy flags).
     */
    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        // Security check: Employee must not be soft-deleted
        if ($employee->trashed()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Pegawai yang sudah dihapus tidak dapat diaktifkan atau dikonfigurasi.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Pegawai yang sudah dihapus tidak dapat diaktifkan atau dikonfigurasi.');
        }

        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
            'show_email' => ['nullable', 'boolean'],
            'show_photo' => ['nullable', 'boolean'],
            'show_department' => ['nullable', 'boolean'],
            'show_location' => ['nullable', 'boolean'],
            'custom_title' => ['nullable', 'string', 'max:255'],
        ]);

        $card = $this->cardService->getOrCreateCard($employee);

        $attributes = [
            'is_active' => $request->boolean('is_active'),
            'show_phone' => $request->boolean('show_phone'),
            'show_email' => $request->boolean('show_email'),
            'show_photo' => $request->boolean('show_photo'),
            'show_department' => $request->boolean('show_department'),
            'show_location' => $request->boolean('show_location'),
            'custom_title' => $validated['custom_title'] ?? null,
        ];

        // Security check: show_location cannot be enabled if company office address is unconfigured or placeholder
        if ($attributes['show_location'] && ! $this->cardService->isOfficeAddressConfigured()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Alamat kantor pusat resmi belum dikonfigurasi.',
                    'errors' => [
                        'show_location' => ['Alamat kantor pusat resmi belum dikonfigurasi di sistem.'],
                    ],
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'show_location' => 'Alamat kantor pusat resmi belum dikonfigurasi di sistem.',
                ])
                ->with('error', 'Gagal memperbarui: Alamat kantor pusat resmi belum dikonfigurasi.');
        }

        // Fail-closed security rule: Cards can only be activated if employee is eligible
        if ($attributes['is_active']) {
            if (! $this->cardService->isEligibleForPublic($employee)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Kartu tidak dapat diaktifkan: status atau masa kontrak pegawai tidak memenuhi syarat publik.',
                        'errors' => [
                            'is_active' => ['Status atau masa kontrak pegawai saat ini tidak memenuhi syarat untuk dipublikasikan.'],
                        ],
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'is_active' => 'Status atau masa kontrak pegawai saat ini tidak memenuhi syarat untuk dipublikasikan.',
                    ])
                    ->with('error', 'Gagal mengaktifkan kartu: pegawai tidak memenuhi syarat publik (misal: masa kontrak tidak aktif).');
            }
        }

        $this->cardService->updateSettings($card, $attributes);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Digital Business Card berhasil diperbarui.',
                'card' => $card->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan Digital Business Card berhasil diperbarui.');
    }

    /**
     * Download the employee's QR Code PNG.
     */
    public function downloadQr(Employee $employee)
    {
        Gate::authorize('view', $employee);

        $card = $this->cardService->getOrCreateCard($employee);
        $png = $this->cardService->getCardQrPng($card);

        $filename = 'rapid-business-card-qr.png';

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Download or stream print-ready 85x55mm Business Card PDF.
     */
    public function downloadPdf(Employee $employee)
    {
        Gate::authorize('view', $employee);

        $card = $this->cardService->getOrCreateCard($employee);
        $pdf = $this->cardService->generatePdf($employee, $card);

        $filename = 'rapid-business-card.pdf';

        if (request()->query('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
