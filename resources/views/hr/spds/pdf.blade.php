<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        SPD - {{ $spd->spd_number }}
    </title>

    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ---------------------------------------------------------
        | Header
        |--------------------------------------------------------- */

        .header {
            width: 100%;
            border-bottom: 1.5px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo {
            width: 75px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
        }

        .system-name {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .document-title {
            font-size: 12px;
            font-weight: bold;
        }

        .document-number {
            font-size: 8px;
            color: #555;
            margin-top: 3px;
        }

        .submitted {
            font-size: 8px;
            color: #555;
            margin-top: 2px;
        }

        /* ---------------------------------------------------------
         | Section
         |--------------------------------------------------------- */

        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            background-color: #eeeeee;
            border: 1px solid #000;
            padding: 4px 5px;
            margin-top: 7px;
        }

        /* ---------------------------------------------------------
         | General Table
         |--------------------------------------------------------- */

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }

        table.data th {
            width: 22%;
            text-align: left;
            font-weight: bold;
            background-color: #f7f7f7;
        }

        .amount {
            text-align: right;
        }

        /* ---------------------------------------------------------
         | Financial Information
         |--------------------------------------------------------- */

        .financial-table {
            width: 100%;
            border-collapse: collapse;
        }

        .financial-table th,
        .financial-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }

        .financial-table th {
            width: 22%;
            text-align: left;
            font-weight: bold;
            background-color: #f7f7f7;
        }

        .financial-table .value {
            width: 28%;
        }

        .financial-table .right-label {
            width: 22%;
        }

        .financial-table .right-value {
            width: 28%;
            text-align: right;
        }

        /* ---------------------------------------------------------
         | Approval
         |--------------------------------------------------------- */

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .approval-table th,
        .approval-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .approval-table th {
            text-align: center;
            background-color: #f7f7f7;
            font-size: 8.5px;
        }

        .approval-content {
            text-align: center;
        }

        .approval-status {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 9px;
        }

        .approval-name {
            font-weight: bold;
            margin-top: 2px;
        }

        .approval-role {
            font-size: 8px;
            margin-top: 2px;
        }

        .status-approved {
            color: green;
        }

        .status-rejected {
            color: red;
        }

        .status-pending {
            color: #555;
        }

        .approval-date {
            font-size: 7.5px;
            margin-top: 3px;
        }

        .approval-reason {
            margin-top: 5px;
            padding: 4px;
            border-top: 1px solid #999;
            font-size: 7.5px;
            text-align: left;
        }

        .approval-reason strong {
            display: block;
            margin-bottom: 2px;
        }

        /* ---------------------------------------------------------
         | Footer
         |--------------------------------------------------------- */

        .footer {
            margin-top: 8px;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>

{{-- =========================================================
    HEADER
========================================================== --}}

<div class="header">

    <table class="header-table">

        <tr>

            {{-- LEFT : LOGO + COMPANY NAME --}}
            <td
                style="
                    width: 55%;
                    vertical-align: middle;
                "
            >

                @if(file_exists(public_path('images/company-logo.png')))

                    <img
                        src="{{ public_path('images/company-logo.png') }}"
                        class="logo"
                    >

                @endif

                <div class="company-name">
                    PT RAPID INFRASTRUKTUR INDONESIA
                </div>

                <div class="system-name">
                    Engineering Information Management System
                </div>

            </td>


            {{-- RIGHT : DOCUMENT INFORMATION --}}
            <td
                style="
                    width: 45%;
                    text-align: right;
                    vertical-align: middle;
                "
            >

                <div class="document-title">
                    SURAT PERJALANAN DINAS
                </div>

                <div class="document-number">
                    SPD Number:
                    {{ $spd->spd_number ?? '-' }}
                </div>

                <div class="submitted">
                    Submitted:
                    {{ $spd->created_at?->format('d M Y H:i') ?? '-' }}
                </div>

            </td>

        </tr>

    </table>

</div>


    {{-- =========================================================
        EMPLOYEE INFORMATION
    ========================================================== --}}

    <div class="section-title">
        EMPLOYEE INFORMATION
    </div>

    <table class="data">

        <tr>

            <th>
                Employee Name
            </th>

            <td>
                {{ $spd->employee->full_name ?? '-' }}
            </td>

            <th>
                NIK
            </th>

            <td>
                {{ $spd->employee->nik ?? '-' }}
            </td>

        </tr>

        <tr>

            <th>
                Division
            </th>

            <td>
                {{ $spd->employee->division->name ?? '-' }}
            </td>

            <th>
                Position
            </th>

            <td>
                {{ $spd->employee->position->name ?? '-' }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
        BUSINESS TRIP INFORMATION
    ========================================================== --}}

    <div class="section-title">
        BUSINESS TRIP INFORMATION
    </div>

    <table class="data">

        <tr>

            <th>
                Travel Type
            </th>

            <td>
                {{ ucfirst($spd->travel_type) }}
            </td>

            <th>
                Transportation
            </th>

            <td>
                {{ ucfirst($spd->transportation) }}
            </td>

        </tr>

        <tr>

            <th>
                From
            </th>

            <td>
                {{ $spd->from }}
            </td>

            <th>
                Destination
            </th>

            <td>
                {{ $spd->destination }}
            </td>

        </tr>

        <tr>

            <th>
                Departure Date
            </th>

            <td>
                {{ \Carbon\Carbon::parse($spd->date_departure)->format('d F Y') }}
            </td>

            <th>
                Return Date
            </th>

            <td>
                {{ \Carbon\Carbon::parse($spd->date_return)->format('d F Y') }}
            </td>

        </tr>

        <tr>

            <th>
                Purpose
            </th>

            <td colspan="3">
                {{ $spd->purpose }}
            </td>

        </tr>

        @if($spd->note)

            <tr>

                <th>
                    Note
                </th>

                <td colspan="3">
                    {{ $spd->note }}
                </td>

            </tr>

        @endif

    </table>


    {{-- =========================================================
        PROJECT INFORMATION
    ========================================================== --}}

    <div class="section-title">
        PROJECT INFORMATION
    </div>

    <table class="data">

        <tr>

            <th>
                Project
            </th>

            <td>
                {{ $spd->project->name ?? '-' }}
            </td>

            <th>
                Cost Center
            </th>

            <td>
                {{ $spd->project->cost_center ?? '-' }}
            </td>

        </tr>

        <tr>

            <th>
                Approval Document
            </th>

            <td colspan="3">
                {{ $spd->approvalDocument->full_name ?? '-' }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
        FINANCIAL INFORMATION
    ========================================================== --}}

    <div class="section-title">
        FINANCIAL INFORMATION
    </div>

    <table class="financial-table">

        <tr>

            <th>
                Total Days
            </th>

            <td class="value">
                {{ $spd->total_days }} day(s)
            </td>

            <th class="right-label">
                Advance Payment
            </th>

            <td class="right-value">
                {{ $spd->advance_payment ? 'Yes' : 'No' }}
            </td>

        </tr>

        <tr>

            <th>
                Meals / Day
            </th>

            <td class="value amount">
                Rp
                {{ number_format(
                    $spd->meals_per_day,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

            <th class="right-label">
                Total Meals
            </th>

            <td class="right-value">
                Rp
                {{ number_format(
                    $spd->meals_per_day * $spd->total_days,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>

        <tr>

            <th>
                Allowance / Day
            </th>

            <td class="value amount">
                Rp
                {{ number_format(
                    $spd->allowance_per_day,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

            <th class="right-label">
                Total Allowance
            </th>

            <td class="right-value">
                Rp
                {{ number_format(
                    $spd->allowance_per_day * $spd->total_days,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>

        <tr>

            <th>
                &nbsp;
            </th>

            <td class="value">
                &nbsp;
            </td>

            <th class="right-label">
                Contingencies
            </th>

            <td class="right-value">
                Rp
                {{ number_format(
                    $spd->contingencies ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>

        <tr>

            <th>
                &nbsp;
            </th>

            <td class="value">
                &nbsp;
            </td>

            <th class="right-label">
                Local Transport
            </th>

            <td class="right-value">
                Rp
                {{ number_format(
                    $spd->local_transport ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>

        <tr>

            <th>
                &nbsp;
            </th>

            <td class="value">
                &nbsp;
            </td>

            <th class="right-label">
                Balance Received
            </th>

            <td class="right-value">

                <strong>
                    Rp
                    {{ number_format(
                        $spd->balance_received,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>

            </td>

        </tr>

    </table>


    {{-- =========================================================
        APPROVAL
    ========================================================== --}}

    <div class="section-title">
        APPROVAL
    </div>

    <table class="approval-table">

        <tr>

            <th style="width: 33.33%;">
                REQUEST BY HR
            </th>

            <th style="width: 33.33%;">
                MANAGER APPROVAL
            </th>

            <th style="width: 33.33%;">
                COST CENTER USER
            </th>

        </tr>

        <tr>

            {{-- =================================================
                 REQUEST BY HR
            ================================================== --}}

            <td>

                <div class="approval-content">

                    <div class="approval-status status-approved">
                        Submitted
                    </div>

                    <div class="approval-name">
                        {{ $spd->creator->employee->full_name ?? '-' }}
                    </div>

                    <div class="approval-role">
                        HRD
                    </div>

                    <div class="approval-date">
                        {{ $spd->created_at?->format('d F Y H:i') ?? '-' }}
                    </div>

                </div>

            </td>


            {{-- =================================================
                 MANAGER APPROVAL
            ================================================== --}}

            <td>

                <div class="approval-content">

                    @if ($spd->status === 'pending_manager')

                        <div class="approval-status status-pending">
                            Pending
                        </div>

                    @elseif (
                        $spd->manager_approved_at
                        || $spd->status === 'pending_document'
                        || $spd->status === 'approved'
                    )

                        <div class="approval-status status-approved">
                            Approved
                        </div>

                    @elseif (
                        $spd->status === 'rejected'
                        && $spd->manager_rejection_reason
                    )

                        <div class="approval-status status-rejected">
                            Rejected
                        </div>

                    @else

                        <div class="approval-status status-pending">
                            Pending
                        </div>

                    @endif


                    <div class="approval-name">
                        {{ $spd->manager->full_name ?? '-' }}
                    </div>

                    <div class="approval-role">
                        {{ $spd->manager->position->name ?? 'Manager' }}
                    </div>


                    @if ($spd->manager_approved_at)

                        <div class="approval-date">
                            {{ $spd->manager_approved_at->format('d F Y H:i') }}
                        </div>

                    @endif


                    @if (
                        $spd->status === 'rejected'
                        && $spd->manager_rejection_reason
                    )

                        <div class="approval-reason">

                            <strong>
                                Rejection Reason:
                            </strong>

                            {{ $spd->manager_rejection_reason }}

                        </div>

                    @endif

                </div>

            </td>


            {{-- =================================================
                 COST CENTER USER
            ================================================== --}}

            <td>

                <div class="approval-content">

                    @if ($spd->status === 'pending_manager')

                        <div class="approval-status status-pending">
                            Pending
                        </div>

                    @elseif ($spd->status === 'pending_document')

                        <div class="approval-status status-pending">
                            Pending
                        </div>

                    @elseif ($spd->status === 'approved')

                        <div class="approval-status status-approved">
                            Approved
                        </div>

                    @elseif (
                        $spd->status === 'rejected'
                        && $spd->document_rejection_reason
                    )

                        <div class="approval-status status-rejected">
                            Rejected
                        </div>

                    @else

                        <div class="approval-status status-pending">
                            Pending
                        </div>

                    @endif


                    <div class="approval-name">
                        {{ $spd->approvalDocument->full_name ?? '-' }}
                    </div>

                    <div class="approval-role">
                        {{ $spd->approvalDocument->position->name ?? 'Cost Center User' }}
                    </div>


                    @if ($spd->document_approved_at)

                        <div class="approval-date">
                            {{ $spd->document_approved_at->format('d F Y H:i') }}
                        </div>

                    @endif


                    @if (
                        $spd->status === 'rejected'
                        && $spd->document_rejection_reason
                    )

                        <div class="approval-reason">

                            <strong>
                                Rejection Reason:
                            </strong>

                            {{ $spd->document_rejection_reason }}

                        </div>

                    @endif

                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
        OVERALL STATUS
    ========================================================== --}}

    <div class="section-title">
        SPD STATUS
    </div>

    <table class="data">

        <tr>

            <th>
                Current Status
            </th>

            <td>

                @if ($spd->status === 'approved')

                    <span class="status-approved">
                        Approved
                    </span>

                @elseif ($spd->status === 'rejected')

                    <span class="status-rejected">
                        Rejected
                    </span>

                @elseif ($spd->status === 'pending_manager')

                    <span class="status-pending">
                        Pending Manager Approval
                    </span>

                @elseif ($spd->status === 'pending_document')

                    <span class="status-pending">
                        Pending Cost Center Approval
                    </span>

                @else

                    <span class="status-pending">
                        Pending
                    </span>

                @endif

            </td>

        </tr>


        @if (
            $spd->status === 'rejected'
            && $spd->manager_rejection_reason
        )

            <tr>

                <th>
                    Rejection Stage
                </th>

                <td>
                    Manager
                </td>

            </tr>

            <tr>

                <th>
                    Rejection Reason
                </th>

                <td>
                    {{ $spd->manager_rejection_reason }}
                </td>

            </tr>

        @elseif (
            $spd->status === 'rejected'
            && $spd->document_rejection_reason
        )

            <tr>

                <th>
                    Rejection Stage
                </th>

                <td>
                    Cost Center User
                </td>

            </tr>

            <tr>

                <th>
                    Rejection Reason
                </th>

                <td>
                    {{ $spd->document_rejection_reason }}
                </td>

            </tr>

        @endif

    </table>


    {{-- =========================================================
        FOOTER
    ========================================================== --}}

    <div class="footer">

        This document is generated by Engineering Information Management System (EIMS).

    </div>

</body>

</html>
