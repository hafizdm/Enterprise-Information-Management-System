<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        SPD Travel Report
    </title>

    <style>

        @page {
            margin: 30px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
            line-height: 1.3;
        }

        /* ===================================================== */
        /* HEADER */
        /* ===================================================== */

        .header {
            width: 100%;
            border-bottom: 2px solid #222;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
        }

        .logo {
            width: 75px;
        }

        .company-name {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
        }

        .system-name {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }

        .document-title {
            text-align: right;
            font-size: 15px;
            font-weight: bold;
        }

        .document-number {
            text-align: right;
            font-size: 8px;
            margin-top: 4px;
            color: #555;
        }

        /* ===================================================== */
        /* SECTION */
        /* ===================================================== */

        .section-title {
            background: #eeeeee;
            border: 1px solid #cccccc;
            padding: 5px 6px;
            font-weight: bold;
            font-size: 9px;
            margin-top: 9px;
        }

        /* ===================================================== */
        /* INFORMATION TABLE */
        /* ===================================================== */

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            border: 1px solid #cccccc;
            padding: 4px 5px;
        }

        .label {
            width: 20%;
            background: #f7f7f7;
            font-weight: bold;
        }

        .value {
            width: 30%;
        }

        /* ===================================================== */
        /* FINANCIAL TABLE */
        /* ===================================================== */

        .financial-table {
            width: 100%;
            border-collapse: collapse;
        }

        .financial-table td {
            border: 1px solid #cccccc;
            padding: 4px 5px;
        }

        .financial-label {
            width: 25%;
        }

        .financial-value {
            width: 25%;
            text-align: right;
            white-space: nowrap;
        }

        .financial-total {
            font-weight: bold;
            background: #f7f7f7;
        }

        /* ===================================================== */
        /* SETTLEMENT */
        /* ===================================================== */

        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .settlement-table td {
            border: 1px solid #cccccc;
            padding: 4px 5px;
        }

        .settlement-label {
            width: 25%;
            background: #f7f7f7;
            font-weight: bold;
        }

        /* ===================================================== */
        /* STATUS */
        /* ===================================================== */

        .status {
            font-weight: bold;
        }

        .status-pending {
            color: #856404;
        }

        .status-approved {
            color: #155724;
        }

        .status-rejected {
            color: #721c24;
        }

        /* ===================================================== */
        /* NOTE */
        /* ===================================================== */

        .note {
            border: 1px solid #cccccc;
            padding: 5px;
            min-height: 25px;
        }

        /* ===================================================== */
        /* APPROVAL */
        /* ===================================================== */

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .approval-table th,
        .approval-table td {
            border: 1px solid #cccccc;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        .approval-table th {
            background: #eeeeee;
        }

        .signature-space {
            height: 28px;
        }

        /* ===================================================== */
        /* REJECTION */
        /* ===================================================== */

        .rejection-reason {
            margin-top: 6px;
            border: 1px solid #cccccc;
            padding: 5px;
        }

        .rejection-reason-title {
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* ===================================================== */
        /* FOOTER */
        /* ===================================================== */

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #777;
        }

    </style>

</head>


<body>


{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="header">

    <table class="header-table">

        <tr>

            {{-- COMPANY --}}
            <td style="width: 55%; vertical-align: middle;">

                @php
                    $logoPath = public_path('images/company-logo.png');
                @endphp

                @if(file_exists($logoPath))

                    <img
                        src="{{ $logoPath }}"
                        class="logo"
                    >

                @endif


                <div class="company-name">
                    PT Rapid Infrastruktur Indonesia
                </div>


                <div class="system-name">
                    ENGINEERING INFORMATION MANAGEMENT SYSTEM
                </div>

            </td>


            {{-- DOCUMENT --}}
            <td style="width: 45%; vertical-align: middle;">

                <div class="document-title">
                    SPD TRAVEL REPORT
                </div>


                <div class="document-number">

                    SPD Number:

                    <strong>
                        {{ $spdReport->spd?->spd_number ?? '-' }}
                    </strong>

                </div>


                <div class="document-number">

                    Submitted:

                    {{ $spdReport->submitted_at
                        ? $spdReport->submitted_at->format('d M Y H:i')
                        : '-' }}

                </div>

            </td>

        </tr>

    </table>

</div>


{{-- ========================================================= --}}
{{-- EMPLOYEE INFORMATION --}}
{{-- ========================================================= --}}

<div class="section-title">
    EMPLOYEE INFORMATION
</div>


<table class="info-table">

    <tr>

        <td class="label">
            NIK
        </td>

        <td class="value">
            {{ $spdReport->employee?->nik ?? '-' }}
        </td>


        <td class="label">
            Employee Name
        </td>

        <td class="value">
            {{ $spdReport->employee?->full_name ?? '-' }}
        </td>

    </tr>


    <tr>

        <td class="label">
            Division
        </td>

        <td class="value">
            {{ $spdReport->employee?->division?->name ?? '-' }}
        </td>


        <td class="label">
            Position
        </td>

        <td class="value">
            {{ $spdReport->employee?->position?->name ?? '-' }}
        </td>

    </tr>


    <tr>

        <td class="label">
            Project
        </td>

        <td
            class="value"
            colspan="3"
        >

            {{ $spdReport->spd?->project?->name ?? '-' }}

        </td>

    </tr>

</table>


{{-- ========================================================= --}}
{{-- BUSINESS TRIP INFORMATION --}}
{{-- ========================================================= --}}

<div class="section-title">
    BUSINESS TRIP INFORMATION
</div>


<table class="info-table">

    <tr>

        <td class="label">
            Travel Type
        </td>

        <td class="value">
            {{ ucfirst($spdReport->spd?->travel_type ?? '-') }}
        </td>


        <td class="label">
            Transportation
        </td>

        <td class="value">
            {{ ucfirst($spdReport->spd?->transportation ?? '-') }}
        </td>

    </tr>


    <tr>

        <td class="label">
            From
        </td>

        <td class="value">
            {{ $spdReport->spd?->from ?? '-' }}
        </td>


        <td class="label">
            Destination
        </td>

        <td class="value">
            {{ $spdReport->spd?->destination ?? '-' }}
        </td>

    </tr>


    <tr>

        <td class="label">
            Departure Date
        </td>

        <td class="value">

            {{ $spdReport->date_departure
                ? $spdReport->date_departure->format('d M Y')
                : '-' }}

        </td>


        <td class="label">
            Return Date
        </td>

        <td class="value">

            {{ $spdReport->date_return
                ? $spdReport->date_return->format('d M Y')
                : '-' }}

        </td>

    </tr>


    <tr>

        <td class="label">
            Total Days
        </td>

        <td class="value">

            {{ $spdReport->total_days ?? 0 }}
            day(s)

        </td>


        <td class="label">
            Purpose
        </td>

        <td class="value">

            {{ $spdReport->spd?->purpose ?? '-' }}

        </td>

    </tr>

</table>


{{-- ========================================================= --}}
{{-- FINANCIAL INFORMATION - SPD --}}
{{-- ========================================================= --}}

<div class="section-title">
    FINANCIAL INFORMATION (SPD)
</div>


<table class="financial-table">

    <tr>

        <td class="financial-label">
            Total Days
        </td>

        <td class="financial-value">

            {{ number_format(
                (float) ($spdReport->spd?->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

            day(s)

        </td>


        <td class="financial-label">
            Advance Payment
        </td>

        <td class="financial-value">

            {{ ucfirst(
                $spdReport->spd?->advance_payment ?? '-'
            ) }}

        </td>

    </tr>


    <tr>

        <td class="financial-label">
            Meals / Day
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->meals_per_day ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>


        <td class="financial-label">
            Total Meals
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->meals_per_day ?? 0)
                *
                (float) ($spdReport->spd?->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td class="financial-label">
            Allowance / Day
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->allowance_per_day ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>


        <td class="financial-label">
            Total Allowance
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->allowance_per_day ?? 0)
                *
                (float) ($spdReport->spd?->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td colspan="2"></td>

        <td class="financial-label">
            Contingencies
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->contingencies ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td colspan="2"></td>

        <td class="financial-label">
            Local Transport
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->local_transport ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr class="financial-total">

        <td colspan="2"></td>

        <td class="financial-label">
            Balance Received
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->spd?->balance_received ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>

</table>


{{-- ========================================================= --}}
{{-- EXPENSE REPORT --}}
{{-- ========================================================= --}}

<div class="section-title">
    EXPENSE REPORT (SPD REPORT / ACTUAL)
</div>


<table class="financial-table">

    <tr>

        <td class="financial-label">
            Total Days Actual
        </td>

        <td class="financial-value">

            {{ number_format(
                (float) ($spdReport->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

            day(s)

        </td>


        <td></td>

        <td></td>

    </tr>


    <tr>

        <td class="financial-label">
            Meals / Day
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->meals_per_day ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>


        <td class="financial-label">
            Total Meals
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->meals_per_day ?? 0)
                *
                (float) ($spdReport->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td class="financial-label">
            Allowance / Day
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->allowance_per_day ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>


        <td class="financial-label">
            Total Allowance
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->allowance_per_day ?? 0)
                *
                (float) ($spdReport->total_days ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td colspan="2"></td>

        <td class="financial-label">
            Contingencies
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->contingencies ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr>

        <td colspan="2"></td>

        <td class="financial-label">
            Local Transport
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->local_transport ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>


    <tr class="financial-total">

        <td colspan="2"></td>

        <td class="financial-label">
            Expense Report
        </td>

        <td class="financial-value">

            Rp
            {{ number_format(
                (float) ($spdReport->expense_balance ?? 0),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>

</table>


{{-- ========================================================= --}}
{{-- SETTLEMENT --}}
{{-- ========================================================= --}}

<table class="settlement-table">

    <tr>

        <td class="settlement-label">
            Settlement Status
        </td>

        <td>

            {{ ucwords(
                str_replace(
                    '_',
                    ' ',
                    $spdReport->settlement_status ?? '-'
                )
            ) }}

        </td>


        <td class="settlement-label">
            Settlement Amount
        </td>

        <td>

            Rp
            {{ number_format(
                abs((float) ($spdReport->expense_report_total ?? 0)),
                0,
                ',',
                '.'
            ) }}

        </td>

    </tr>

</table>


{{-- ========================================================= --}}
{{-- REPORT NOTE --}}
{{-- ========================================================= --}}

<div class="section-title">
    REPORT NOTE
</div>


<div class="note">

    {{ $spdReport->note ?? '-' }}

</div>


{{-- ========================================================= --}}
{{-- REJECTION --}}
{{-- ========================================================= --}}

@if(
    $spdReport->status_report === 'rejected'
    && $spdReport->manager_rejection_reason
)

    <div class="rejection-reason">

        <div class="rejection-reason-title">
            Manager Rejection Reason
        </div>

        <div>
            {{ $spdReport->manager_rejection_reason }}
        </div>

    </div>

@endif


{{-- ========================================================= --}}
{{-- APPROVAL --}}
{{-- ========================================================= --}}

<div class="section-title">
    APPROVAL
</div>


<table class="approval-table">

    <thead>

        <tr>

            <th style="width: 33%;">
                Requestor
            </th>

            <th style="width: 33%;">
                Manager Approval
            </th>

            <th style="width: 34%;">
                Mengetahui HR
            </th>

        </tr>

    </thead>


    <tbody>

        <tr>

            {{-- REQUESTOR --}}
            <td>

                <strong>
                    {{ $spdReport->employee?->full_name ?? '-' }}
                </strong>


                <div class="signature-space"></div>


                <strong class="status-approved">
                    Submitted
                </strong>


                <br>


                <small>
                    {{ $spdReport->employee?->position?->name ?? '-' }}
                </small>

            </td>


            {{-- MANAGER --}}
            <td>

                <strong>
                    {{ $spdReport->spd?->manager?->full_name ?? '-' }}
                </strong>


                <div class="signature-space"></div>


                @if($spdReport->status_report === 'approved')

                    <strong class="status-approved">
                        Approved
                    </strong>

                    @if($spdReport->manager_approved_at)

                        <br>

                        <small>
                            {{ $spdReport->manager_approved_at->format('d M Y H:i') }}
                        </small>

                    @endif

                @elseif($spdReport->status_report === 'rejected')

                    <strong class="status-rejected">
                        Rejected
                    </strong>

                @else

                    <strong class="status-pending">
                        Pending Approval
                    </strong>

                @endif


                <br>


                <small>
                    {{ $spdReport->spd?->manager?->position?->name ?? '-' }}
                </small>

            </td>


            {{-- HR --}}
            <td>

                <strong>
                    {{ $spdReport->spd?->creator?->employee?->full_name ?? '-' }}
                </strong>


                <div class="signature-space"></div>


                <strong>
                    Mengetahui
                </strong>


                <br>


                <small>
                    {{ $spdReport->spd?->creator?->employee?->position?->name ?? '-' }}
                </small>

            </td>

        </tr>

    </tbody>

</table>


{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<div class="footer">

    EIMS - SPD Travel Report

</div>


</body>

</html>
