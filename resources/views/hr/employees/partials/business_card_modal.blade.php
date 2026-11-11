@inject('cardService', 'App\Services\BusinessCard\DigitalBusinessCardService')

@php
    $card = $card ?? $cardService->getOrCreateCard($employee);
    $qrSvg = $qrSvg ?? $cardService->getCardQrSvg($card);
    $publicUrl = $publicUrl ?? $cardService->getPublicUrl($card);
    $isEligible = $isEligible ?? $cardService->isEligibleForPublic($employee);
    $isAddressConfigured = $cardService->isOfficeAddressConfigured();
    $canUpdate = auth()->user()?->can('update', $employee) ?? false;
@endphp

<style>
    #digitalBusinessCardModal .qr-preview-frame {
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
        padding: 12px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    #digitalBusinessCardModal .qr-svg-container {
        width: 100%;
        max-width: 150px;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        overflow: hidden;
    }

    #digitalBusinessCardModal .qr-svg-container svg {
        width: 100% !important;
        height: auto !important;
        max-width: 100% !important;
        max-height: 100% !important;
        aspect-ratio: 1 / 1;
        display: block !important;
        margin: 0 auto !important;
    }

    #digitalBusinessCardModal .card-preview-col {
        overflow-x: hidden;
    }
</style>

<div class="modal fade" id="digitalBusinessCardModal" tabindex="-1" aria-labelledby="digitalBusinessCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            {{-- MODAL HEADER --}}
            <div class="modal-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 bg-light p-2 text-dark">
                        <i class="bi bi-qr-code fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="digitalBusinessCardModalLabel">
                            Digital Business Card
                        </h5>
                        <small class="text-muted">
                            {{ $employee->full_name }} — {{ $employee->position?->name ?? 'Employee' }}
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- MODAL BODY --}}
            <div class="modal-body p-4">
                <div class="row g-4">

                    {{-- LEFT COLUMN: CARD VISUAL & QR PREVIEW --}}
                    <div class="col-lg-5 card-preview-col">
                        <div class="card border rounded-3 p-3 h-100 bg-light d-flex flex-column justify-content-between">

                            {{-- Brand & Header --}}
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">
                                        Preview Kartu
                                    </span>

                                    <span id="cardActiveBadge" class="badge {{ $card->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} px-2 py-1">
                                        @if($card->is_active)
                                            <i class="bi bi-check-circle me-1"></i> Aktif (Publik)
                                        @else
                                            <i class="bi bi-eye-slash me-1"></i> Non-Aktif (Privat)
                                        @endif
                                    </span>
                                </div>

                                {{-- Mini Corporate Card Preview --}}
                                <div class="rounded-3 p-3 text-white mb-3 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="fw-bold small text-white-50">
                                            PT RAPID INFRASTRUKTUR INDONESIA
                                        </div>
                                    </div>

                                    <div class="fw-bold fs-6 mb-1 text-white">
                                        {{ $employee->full_name }}
                                    </div>

                                    <div class="text-white-50 small mb-2" id="previewCardCustomTitle">
                                        {{ $card->custom_title ?: ($employee->position?->name ?? 'Staff') }}
                                    </div>

                                    <div class="small text-white-50 border-top border-white-50 pt-2 d-flex justify-content-between">
                                        <span>{{ $employee->division?->name ?? '-' }}</span>
                                        <span>RAPID</span>
                                    </div>
                                </div>

                                {{-- QR Code Responsive Frame --}}
                                <div class="qr-preview-frame mb-3">
                                    <div class="qr-svg-container">
                                        {!! $qrSvg !!}
                                    </div>
                                    <div class="small text-muted mt-2 text-center" style="font-size: 11px;">
                                        <i class="bi bi-upc-scan me-1"></i> Scan untuk membuka profil publik
                                    </div>
                                </div>

                                {{-- Public URL Display & Copy --}}
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">URL Publik Profil</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control font-monospace" id="publicCardUrlInput" value="{{ $publicUrl }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copyPublicUrlBtn" onclick="copyCardUrl()" title="Salin URL">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <span id="copySuccessFeedback" class="small text-success d-none mt-1">
                                        <i class="bi bi-check2"></i> URL berhasil disalin!
                                    </span>
                                </div>
                            </div>

                            {{-- Print PDF & QR Download Actions --}}
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('employees.business-card.pdf', $employee) }}" target="_blank" class="btn btn-primary btn-sm w-100" style="background-color: #0f172a; border-color: #0f172a;">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Kartu Nama (PDF)
                                </a>

                                <a href="{{ route('employees.business-card.qr', $employee) }}" class="btn btn-outline-dark btn-sm w-100">
                                    <i class="bi bi-download me-1"></i> Unduh QR Code (PNG)
                                </a>
                            </div>

                        </div>
                    </div>

                    {{-- RIGHT COLUMN: PRIVACY & VISIBILITY CONFIGURATION --}}
                    <div class="col-lg-7">
                        <form action="{{ route('employees.business-card.update', $employee) }}" method="POST" id="businessCardConfigForm">
                            @csrf
                            @method('PUT')

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">
                                    Pengaturan Privasi & Akses
                                </h6>
                                @if(! $canUpdate)
                                    <span class="badge bg-light text-muted border">
                                        <i class="bi bi-lock me-1"></i> Read-Only
                                    </span>
                                @endif
                            </div>

                            {{-- Dynamic Alert Container --}}
                            <div id="modalAlertContainer"></div>

                            {{-- Ineligibility Warning --}}
                            @if(! $isEligible)
                                <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3" id="ineligibleAlertBox">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                    <div>
                                        <strong>Status Pegawai Tidak Memenuhi Syarat:</strong>
                                        Kartu publik tidak dapat diaktifkan karena masa kontrak telah berakhir atau status kepegawaian tidak valid.
                                    </div>
                                </div>
                            @endif

                            {{-- ACTIVATION SWITCH --}}
                            <div class="card border rounded-3 p-3 mb-3 bg-light">
                                <div class="form-check form-switch mb-0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="is_active_switch"
                                        name="is_active"
                                        value="1"
                                        {{ $card->is_active ? 'checked' : '' }}
                                        {{ (! $canUpdate || ! $isEligible) ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label fw-bold text-dark" for="is_active_switch">
                                        Aktifkan Akses Publik
                                    </label>
                                    <div class="small text-muted">
                                        Jika diaktifkan, profil digital pegawai dapat diakses oleh publik yang memindai QR Code.
                                    </div>
                                </div>
                            </div>

                            {{-- VISIBILITY TOGGLES --}}
                            <div class="mb-3">
                                <span class="small fw-bold text-uppercase text-muted d-block mb-2" style="letter-spacing: 0.5px;">
                                    Informasi yang Ditampilkan ke Publik
                                </span>

                                {{-- Show Phone --}}
                                <div class="form-check form-switch py-2 border-bottom">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="show_phone_switch"
                                        name="show_phone"
                                        value="1"
                                        {{ $card->show_phone ? 'checked' : '' }}
                                        {{ ! $canUpdate ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="show_phone_switch">
                                        <span class="fw-semibold">Nomor Telepon / WhatsApp</span>
                                        <span class="small text-muted d-block font-monospace">
                                            {{ $employee->phone_number ?: 'Belum diisi di data pegawai' }}
                                        </span>
                                    </label>
                                </div>

                                {{-- Show Email --}}
                                <div class="form-check form-switch py-2 border-bottom">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="show_email_switch"
                                        name="show_email"
                                        value="1"
                                        {{ $card->show_email ? 'checked' : '' }}
                                        {{ ! $canUpdate ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="show_email_switch">
                                        <span class="fw-semibold">Email Kerja</span>
                                        <span class="small text-muted d-block font-monospace">
                                            {{ $employee->email }}
                                        </span>
                                    </label>
                                </div>

                                {{-- Show Photo --}}
                                <div class="form-check form-switch py-2 border-bottom">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="show_photo_switch"
                                        name="show_photo"
                                        value="1"
                                        {{ $card->show_photo ? 'checked' : '' }}
                                        {{ ! $canUpdate ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="show_photo_switch">
                                        <span class="fw-semibold">Foto Profil Pegawai</span>
                                        <span class="small text-muted d-block">
                                            @if($employee->photo)
                                                <i class="bi bi-image text-success me-1"></i> Foto profil tersedia
                                            @else
                                                <i class="bi bi-person-x text-muted me-1"></i> Belum ada foto di data pegawai
                                            @endif
                                        </span>
                                    </label>
                                </div>

                                {{-- Show Department / Division --}}
                                <div class="form-check form-switch py-2 border-bottom">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="show_department_switch"
                                        name="show_department"
                                        value="1"
                                        {{ $card->show_department ? 'checked' : '' }}
                                        {{ ! $canUpdate ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="show_department_switch">
                                        <span class="fw-semibold">Divisi / Departemen</span>
                                        <span class="small text-muted d-block">
                                            {{ $employee->division?->name ?: 'Belum diisi di data pegawai' }}
                                        </span>
                                    </label>
                                </div>

                                {{-- Show Office Location (Disabled if unconfigured) --}}
                                <div class="form-check form-switch py-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="show_location_switch"
                                        name="show_location"
                                        value="1"
                                        {{ $card->show_location ? 'checked' : '' }}
                                        {{ (! $canUpdate || ! $isAddressConfigured) ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="show_location_switch">
                                        <span class="fw-semibold">Alamat Kantor Pusat Resmi</span>
                                        @if($isAddressConfigured)
                                            <span class="small text-muted d-block font-monospace">
                                                {{ config('company.office_address') }}
                                            </span>
                                        @else
                                            <span class="small text-warning d-block">
                                                <i class="bi bi-exclamation-circle me-1"></i> Alamat kantor pusat resmi belum dikonfigurasi. Opsi ini terkunci.
                                            </span>
                                        @endif
                                    </label>
                                </div>
                            </div>

                            {{-- Custom Title Override --}}
                            <div class="mb-3">
                                <label for="custom_title_input" class="form-label small fw-semibold">
                                    Gelar / Job Title Khusus (Opsional)
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="custom_title_input"
                                    name="custom_title"
                                    value="{{ old('custom_title', $card->custom_title) }}"
                                    placeholder="{{ $employee->position?->name ?? 'Contoh: Senior Project Manager' }}"
                                    maxlength="255"
                                    {{ ! $canUpdate ? 'disabled' : '' }}
                                >
                                <div class="form-text small">
                                    Kosongkan untuk menggunakan nama posisi standar ({{ $employee->position?->name ?? '-' }}).
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            @if($canUpdate)
                                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                    <button type="submit" class="btn btn-dark btn-sm" id="btnSaveBusinessCard">
                                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            @endif

                        </form>
                    </div>

                </div>
            </div>

            {{-- MODAL FOOTER --}}
            <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="bi bi-shield-check me-1"></i> Token QR permanen dilindungi secara kriptografis (256-bit).
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

<script>
function copyCardUrl() {
    const input = document.getElementById('publicCardUrlInput');
    if (!input) return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(showCopySuccess);
    } else {
        input.select();
        document.execCommand('copy');
        showCopySuccess();
    }
}

function showCopySuccess() {
    const feedback = document.getElementById('copySuccessFeedback');
    if (feedback) {
        feedback.classList.remove('d-none');
        setTimeout(() => feedback.classList.add('d-none'), 3000);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('businessCardConfigForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('btnSaveBusinessCard');
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        }

        const formData = new FormData(form);
        const actionUrl = form.getAttribute('action');

        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    throw errData;
                });
            }
            return response.json();
        })
        .then(data => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }

            // Show feedback alert inside modal
            showModalAlert('success', data.message || 'Pengaturan Digital Business Card berhasil diperbarui.');

            // Update badge in preview header
            const badge = document.getElementById('cardActiveBadge');
            if (badge && data.card) {
                if (data.card.is_active) {
                    badge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-1';
                    badge.innerHTML = '<i class="bi bi-check-circle me-1"></i> Aktif (Publik)';
                } else {
                    badge.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1';
                    badge.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Non-Aktif (Privat)';
                }
            }

            // Update custom title in preview card if modified
            const previewTitle = document.getElementById('previewCardCustomTitle');
            if (previewTitle && data.card) {
                previewTitle.textContent = data.card.custom_title || '{{ $employee->position?->name ?? "Staff" }}';
            }
        })
        .catch(error => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }

            let msg = 'Gagal memperbarui pengaturan.';
            if (error && error.message) {
                msg = error.message;
            } else if (error && error.errors) {
                msg = Object.values(error.errors).flat().join('<br>');
            }
            showModalAlert('danger', msg);
        });
    });

    function showModalAlert(type, message) {
        const container = document.getElementById('modalAlertContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show py-2 px-3 small d-flex align-items-center justify-content-between mb-3" role="alert">
                <div>
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'} me-1"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        if (type === 'success') {
            setTimeout(() => {
                const alertEl = container.querySelector('.alert');
                if (alertEl) {
                    alertEl.classList.remove('show');
                    setTimeout(() => container.innerHTML = '', 300);
                }
            }, 4000);
        }
    }
});
</script>
