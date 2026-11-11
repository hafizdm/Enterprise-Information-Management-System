<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>{{ $employee->full_name }} — PT Rapid Infrastruktur Indonesia</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Bootstrap 5 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --rapid-navy-dark: #0F172A;
            --rapid-navy-blue: #1E3A8A;
            --rapid-white: #FFFFFF;
            --rapid-light-gray: #F1F5F9;
            --rapid-border-gray: #E2E8F0;
            --rapid-text-dark: #111827;
            --rapid-text-secondary: #64748B;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F1F5F9;
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 12px;
            margin: 0;
        }

        .card-wrapper {
            width: 100%;
            max-width: 440px;
            background: #FFFFFF;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
        }

        /* Corporate Hero Header (Navy Gradient) */
        .card-header-hero {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            padding: 34px 24px 62px 24px;
            text-align: center;
            position: relative;
        }

        .company-logo {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }

        /* Avatar / Photo */
        .avatar-container {
            margin-top: -55px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .avatar-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #FFFFFF;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
            background-color: #FFFFFF;
        }

        .avatar-placeholder {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%);
            color: #FFFFFF;
            border: 4px solid #FFFFFF;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        }

        /* Profile Content */
        .profile-content {
            padding: 16px 24px 28px 24px;
            text-align: center;
        }

        .employee-name {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
            line-height: 1.25;
            letter-spacing: -0.3px;
        }

        .employee-title {
            font-size: 14.5px;
            font-weight: 600;
            color: #1E3A8A;
            margin-bottom: 6px;
        }

        .company-name-text {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 22px;
        }

        /* ========================================================
           ACTION BUTTONS (Strict 3-Row Hierarchy)
           ======================================================== */

        /* Row 1: WhatsApp Direct Button */
        .btn-whatsapp-direct {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 15px;
            padding: 13px 20px;
            border-radius: 14px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 10px 20px -5px rgba(37, 211, 102, 0.35);
            transition: all 0.2s ease;
            margin-bottom: 10px;
        }

        .btn-whatsapp-direct:hover, .btn-whatsapp-direct:focus {
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 14px 25px -5px rgba(37, 211, 102, 0.45);
        }

        /* Row 2: Save Contact (VCF) Button */
        .btn-save-contact {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 14px;
            padding: 13px 20px;
            border-radius: 14px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 8px 18px -4px rgba(30, 58, 138, 0.3);
            transition: all 0.2s ease;
            margin-bottom: 12px;
        }

        .btn-save-contact:hover, .btn-save-contact:focus {
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -4px rgba(30, 58, 138, 0.45);
        }

        /* Row 3: Three Equal Quick Action Buttons (Telepon, Email, Bagikan) */
        .quick-actions-row {
            display: flex;
            gap: 10px;
            width: 100%;
            margin-bottom: 24px;
        }

        .quick-action-btn {
            flex: 1 1 0;
            min-width: 0;
            background-color: #F1F5F9;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 12px 6px;
            text-decoration: none;
            color: #111827;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .quick-action-btn i {
            font-size: 20px;
            color: #1E3A8A;
            line-height: 1;
        }

        .quick-action-btn span {
            font-size: 11.5px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
        }

        .quick-action-btn:hover {
            background-color: #E2E8F0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            color: #111827;
        }

        .quick-action-btn:hover i {
            color: #0F172A;
        }

        /* Contact Details Info List */
        .info-list {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
        }

        .info-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.15s ease;
        }

        .info-card:hover {
            background-color: #F1F5F9;
            border-color: #CBD5E1;
            color: inherit;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background-color: #F1F5F9;
            color: #1E3A8A;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
            border: 1px solid #E2E8F0;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .info-val {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        .footer-note {
            font-size: 11px;
            color: #94A3B8;
            margin-top: 20px;
            border-top: 1px solid #F1F5F9;
            padding-top: 16px;
        }

        #shareFeedback {
            display: none;
            font-size: 12px;
            color: #16A34A;
            font-weight: 600;
            margin-top: -14px;
            margin-bottom: 14px;
        }
    </style>
</head>
<body>

<div class="card-wrapper">

    {{-- CORPORATE HERO HEADER (Navy Gradient) --}}
    <div class="card-header-hero">
        @if(file_exists(public_path('images/company-logo-white.png')))
            <img src="{{ asset('images/company-logo-white.png') }}" alt="PT Rapid Infrastruktur Indonesia" class="company-logo">
        @elseif(file_exists(public_path(config('company.logo_path', 'images/company-logo.png'))))
            <img src="{{ asset(config('company.logo_path', 'images/company-logo.png')) }}" alt="PT Rapid Infrastruktur Indonesia" class="company-logo" style="filter: brightness(0) invert(1);">
        @else
            <div class="text-white fw-bold fs-5 tracking-wide">
                PT RAPID INFRASTRUKTUR INDONESIA
            </div>
        @endif
    </div>

    {{-- AVATAR / PHOTO --}}
    <div class="avatar-container">
        @if($card->show_photo && !empty($employee->photo))
            <img src="{{ asset('storage/' . $employee->photo) }}" 
                 alt="{{ $employee->full_name }}" 
                 class="avatar-img"
                 id="employeeAvatarImg"
                 onerror="handleAvatarError(this);">
            <div id="avatarFallback" class="avatar-placeholder d-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="54" height="54" fill="#FFFFFF" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
            </div>
        @else
            <div class="avatar-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" width="54" height="54" fill="#FFFFFF" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- PROFILE CONTENT --}}
    <div class="profile-content">

        {{-- Primary Identity (Name & Position Only — No Division) --}}
        <h1 class="employee-name">{{ $employee->full_name }}</h1>
        <div class="employee-title">{{ $card->custom_title ?: ($employee->position?->name ?? 'Staff') }}</div>
        <div class="company-name-text">PT Rapid Infrastruktur Indonesia</div>

        {{-- Privacy Data Checks --}}
        @php
            $hasPhone = $card->show_phone && !empty($employee->phone_number);
            $hasEmail = $card->show_email && !empty($employee->email);
        @endphp

        {{-- ==================== ROW 1: CHAT WHATSAPP ==================== --}}
        @if($hasPhone && $whatsappUrl)
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-direct">
                <i class="bi bi-whatsapp fs-5 me-2"></i>
                <span>Chat WhatsApp</span>
            </a>
        @endif

        {{-- ==================== ROW 2: SIMPAN KONTAK (VCF) ==================== --}}
        <a href="{{ route('public.business-card.vcf', $card->public_token) }}" class="btn-save-contact">
            <i class="bi bi-person-plus-fill me-2 fs-5"></i>
            <span>Simpan Kontak (VCF)</span>
        </a>

        {{-- ==================== ROW 3: THREE EQUAL BUTTONS (TELEPON, EMAIL, BAGIKAN) ==================== --}}
        <div class="quick-actions-row">
            {{-- Telepon --}}
            @if($hasPhone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $employee->phone_number) }}" class="quick-action-btn" title="Panggil Telepon">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Telepon</span>
                </a>
            @endif

            {{-- Email --}}
            @if($hasEmail)
                <a href="mailto:{{ $employee->email }}" class="quick-action-btn" title="Kirim Email">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Email</span>
                </a>
            @endif

            {{-- Bagikan --}}
            <button type="button" class="quick-action-btn border-0" onclick="shareProfile()" title="Bagikan Kontak">
                <i class="bi bi-share-fill"></i>
                <span>Bagikan</span>
            </button>
        </div>
        <div id="shareFeedback"><i class="bi bi-check2-circle me-1"></i> Tautan berhasil disalin!</div>

        {{-- DETAILED CONTACT INFO LIST --}}
        <div class="info-list">

            {{-- Phone Item --}}
            @if($hasPhone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $employee->phone_number) }}" class="info-card">
                    <div class="info-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <div class="info-label">Nomor Telepon / WhatsApp</div>
                        <div class="info-val">{{ $employee->phone_number }}</div>
                    </div>
                </a>
            @endif

            {{-- Email Item --}}
            @if($hasEmail)
                <a href="mailto:{{ $employee->email }}" class="info-card">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <div class="info-label">Email Perusahaan</div>
                        <div class="info-val">{{ $employee->email }}</div>
                    </div>
                </a>
            @endif

            {{-- Kantor Pusat Resmi (Default Address) --}}
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                    <div class="info-label">Kantor Pusat Resmi</div>
                    <div class="info-val">{{ config('company.office_address') }}</div>
                </div>
            </div>

            {{-- Situs Web Resmi --}}
            @if(config('company.website'))
                <a href="{{ config('company.website') }}" target="_blank" rel="noopener noreferrer" class="info-card">
                    <div class="info-icon">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <div>
                        <div class="info-label">Situs Web Resmi</div>
                        <div class="info-val">{{ config('company.website') }}</div>
                    </div>
                </a>
            @endif

        </div>

        {{-- FOOTER NOTE --}}
        <div class="footer-note">
            &copy; {{ date('Y') }} PT Rapid Infrastruktur Indonesia. All rights reserved.
        </div>

    </div>

</div>

<script>
function handleAvatarError(img) {
    img.onerror = null;
    img.style.display = 'none';
    const fallback = document.getElementById('avatarFallback');
    if (fallback) {
        fallback.classList.remove('d-none');
    }
}

function shareProfile() {
    const shareData = {
        title: '{{ addslashes($employee->full_name) }} — Digital Business Card',
        text: '{{ addslashes($employee->full_name) }} | {{ addslashes($card->custom_title ?: ($employee->position?->name ?? "PT Rapid Infrastruktur Indonesia")) }}',
        url: window.location.href
    };

    if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
        navigator.share(shareData).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const feedback = document.getElementById('shareFeedback');
            if (feedback) {
                feedback.style.display = 'block';
                setTimeout(() => {
                    feedback.style.display = 'none';
                }, 3000);
            }
        }).catch(() => {
            prompt('Salin tautan kartu ini:', window.location.href);
        });
    }
}
</script>

</body>
</html>
