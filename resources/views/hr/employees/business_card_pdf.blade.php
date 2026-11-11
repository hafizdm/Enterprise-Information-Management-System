<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $employee->full_name }} — Official RAPID Business Card</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: 85mm 55mm;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 85mm;
            height: 55mm;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000000;
            -webkit-print-color-adjust: exact;
        }

        /* ========================================================
           PAGE 1: FRONT SIDE (White Design with Bottom Black Strip)
           ======================================================== */
        .card-front {
            width: 85mm;
            height: 55mm;
            position: relative;
            background-color: #ffffff;
            page-break-after: always;
            overflow: hidden;
        }

        .front-content {
            width: 78mm;
            margin-left: 3.5mm;
            margin-right: 3.5mm;
            padding-top: 4.0mm;
        }

        .front-logo {
            height: 7.0mm;
            max-width: 22mm;
            display: block;
        }

        .front-logo-fallback {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .front-logo-fallback span {
            font-size: 5pt;
            display: block;
            letter-spacing: 1.5px;
            font-weight: normal;
        }

        .identity-container {
            margin-top: 11.5mm;
        }

        .employee-name {
            font-size: 10pt;
            font-weight: bold;
            color: #000000;
            line-height: 1.15;
            letter-spacing: -0.15px;
            word-wrap: break-word;
        }

        .employee-title {
            font-size: 7.2pt;
            font-weight: normal;
            color: #111111;
            line-height: 1.2;
            margin-top: 0.5mm;
        }

        .divider-line {
            width: 78mm;
            height: 0.35mm;
            background-color: #000000;
            margin-top: 1.0mm;
            margin-bottom: 1.2mm;
        }

        .bottom-table {
            width: 78mm;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .contact-col {
            width: 61mm;
            vertical-align: top;
            padding-right: 2mm;
        }

        .qr-col {
            width: 17mm;
            vertical-align: top;
            text-align: right;
        }

        .contact-email {
            font-size: 5.1pt;
            color: #0044cc;
            line-height: 1.35;
            margin-bottom: 0.35mm;
            word-wrap: break-word;
        }

        .contact-email a {
            color: #0044cc;
            text-decoration: underline;
        }

        .contact-phone {
            font-size: 5.1pt;
            color: #000000;
            line-height: 1.35;
            margin-bottom: 0.35mm;
        }

        .contact-address {
            font-size: 4.5pt;
            color: #111111;
            line-height: 1.25;
            margin-bottom: 0.35mm;
        }

        .contact-website {
            font-size: 4.9pt;
            color: #000000;
            line-height: 1.25;
        }

        .qr-image {
            width: 14mm;
            height: 14mm;
            display: block;
            margin-left: auto;
        }

        /* BOTTOM BLACK STRIP */
        .bottom-black-strip {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 85mm;
            height: 2.6mm;
            background-color: #000000;
        }

        /* ========================================================
           PAGE 2: BACK SIDE (Solid Black with Centered White Logo)
           ======================================================== */
        .card-back {
            width: 85mm;
            height: 55mm;
            position: relative;
            background-color: #000000;
            overflow: hidden;
        }

        .back-table {
            width: 100%;
            height: 55mm;
            border-collapse: collapse;
            background-color: #000000;
        }

        .back-cell {
            vertical-align: middle;
            text-align: center;
            height: 55mm;
            background-color: #000000;
        }

        .back-logo {
            width: 32mm;
            display: inline-block;
        }

        .back-logo-fallback {
            font-size: 16pt;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .back-logo-fallback span {
            font-size: 7pt;
            display: block;
            letter-spacing: 3px;
            font-weight: normal;
            color: #e2e8f0;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    {{-- ==================== PAGE 1: FRONT SIDE ==================== --}}
    <div class="card-front">
        <div class="front-content">
            {{-- Official RAPID Black Logo (Top-Left) --}}
            @if(!empty($logoBase64))
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="front-logo" alt="RAPID">
            @else
                <div class="front-logo-fallback">RAPID <span>INFRASTRUKTUR</span></div>
            @endif

            {{-- Primary Employee Identity (Name & Position Only) --}}
            <div class="identity-container">
                <div class="employee-name">{{ $employee->full_name }}</div>
                <div class="employee-title">{{ $card->custom_title ?: ($employee->position?->name ?? 'Staff') }}</div>
            </div>

            {{-- Horizontal Black Divider Line --}}
            <div class="divider-line"></div>

            {{-- Contact Details + QR Code Table --}}
            <table class="bottom-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="contact-col">
                        {{-- Corporate Email (Privacy Flag) --}}
                        @if($card->show_email && !empty($employee->email))
                            <div class="contact-email">
                                <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                            </div>
                        @endif

                        {{-- WhatsApp / Phone (Privacy Flag) --}}
                        @if($card->show_phone && !empty($employee->phone_number))
                            <div class="contact-phone">{{ $employee->phone_number }}</div>
                        @endif

                        {{-- Official Office Address (Privacy Flag & Configured) --}}
                        @if($card->show_location && $isAddressConfigured)
                            <div class="contact-address">
                                {{ config('company.office_address') }}
                            </div>
                        @endif

                        {{-- Official Website --}}
                        @if($isWebsiteConfigured)
                            <div class="contact-website">www.rapidinfrastruktur.com</div>
                        @endif
                    </td>

                    {{-- QR Code (100% Intact, Fully Contained with Quiet Zone) --}}
                    <td class="qr-col">
                        <img src="data:image/png;base64,{{ $qrPngBase64 }}" class="qr-image" alt="QR Code">
                    </td>
                </tr>
            </table>
        </div>

        {{-- Bottom Black Strip across card width --}}
        <div class="bottom-black-strip"></div>
    </div>

    {{-- ==================== PAGE 2: BACK SIDE ==================== --}}
    <div class="card-back">
        <table class="back-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="back-cell">
                    @if(!empty($whiteLogoBase64))
                        <img src="data:image/png;base64,{{ $whiteLogoBase64 }}" class="back-logo" alt="RAPID">
                    @elseif(!empty($logoBase64))
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="back-logo" alt="RAPID" style="filter: invert(1);">
                    @else
                        <div class="back-logo-fallback">RAPID <span>INFRASTRUKTUR</span></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
