<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">


<title>Leave Request Form</title>

<style>
    @page {
        margin: 35px 40px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #222;
    }

    .header {
        width: 100%;
        border-bottom: 2px solid #222;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logo {
        width: 90px;
    }

    .company-name {
        font-size: 14px;
        font-weight: bold;
    }

    .system-name {
        font-size: 9px;
        color: #666;
        margin-top: 3px;
    }

    .document-title {
        text-align: right;
        font-size: 16px;
        font-weight: bold;
    }

    .document-number {
        text-align: right;
        font-size: 9px;
        margin-top: 5px;
        color: #555;
    }

    .section-title {
        background: #eeeeee;
        border: 1px solid #cccccc;
        padding: 7px;
        font-weight: bold;
        font-size: 10px;
        margin-top: 15px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        border: 1px solid #cccccc;
        padding: 7px;
    }

    .label {
        width: 20%;
        background: #f7f7f7;
        font-weight: bold;
    }

    .value {
        width: 30%;
    }

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

    .approval-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
    }

    .approval-table th,
    .approval-table td {
        border: 1px solid #cccccc;
        padding: 8px;
        text-align: center;
    }

    .approval-table th {
        background: #eeeeee;
    }

    .signature-space {
        height: 55px;
    }

    .rejection-reason {
        margin-top: 10px;
        border: 1px solid #cccccc;
        padding: 8px;
    }

    .rejection-reason-title {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .footer {
        position: fixed;
        bottom: -15px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8px;
        color: #777;
    }
</style>


</head>

<body>


{{-- HEADER --}}
<div class="header">

    <table class="header-table">
        <tr>

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

            <td style="width: 45%; vertical-align: middle;">

                <div class="document-title">
                    LEAVE REQUEST FORM
                </div>

                <div class="document-number">
                    Request No: LR-{{ str_pad($leaveRequest->id, 6, '0', STR_PAD_LEFT) }}
                </div>

                <div class="document-number">
                    Submitted:
                    {{ $leaveRequest->created_at->format('d M Y H:i') }}
                </div>

            </td>

        </tr>
    </table>

</div>


{{-- EMPLOYEE INFORMATION --}}
<div class="section-title">
    EMPLOYEE INFORMATION
</div>

<table class="info-table">

    <tr>
        <td class="label">NIK</td>
        <td class="value">
            {{ $leaveRequest->employee->nik ?? '-' }}
        </td>

        <td class="label">Employee Name</td>
        <td class="value">
            {{ $leaveRequest->employee->full_name ?? '-' }}
        </td>
    </tr>

    <tr>
        <td class="label">Division</td>
        <td class="value">
            {{ $leaveRequest->employee->division->name ?? '-' }}
        </td>

        <td class="label">Position</td>
        <td class="value">
            {{ $leaveRequest->employee->position->name ?? '-' }}
        </td>
    </tr>

    <tr>
        <td class="label">Project</td>
        <td class="value" colspan="3">
            {{ $leaveRequest->employee->project->name ?? '-' }}
        </td>
    </tr>

</table>


{{-- LEAVE REQUEST --}}
<div class="section-title">
    LEAVE REQUEST
</div>

<table class="info-table">

    <tr>
        <td class="label">Leave Type</td>
        <td class="value">
            {{ ucfirst($leaveRequest->leave_type) }}
        </td>

        <td class="label">Current Status</td>
        <td class="value status">

            @if($leaveRequest->status === 'pending_manager')

                <span class="status-pending">
                    Pending Manager Approval
                </span>

            @elseif($leaveRequest->status === 'approved')

                <span class="status-approved">
                    Approved
                </span>

            @elseif($leaveRequest->status === 'rejected')

                <span class="status-rejected">
                    Rejected
                </span>

            @else

                {{ ucfirst($leaveRequest->status) }}

            @endif

        </td>
    </tr>

    <tr>
        <td class="label">First Date</td>
        <td class="value">
            {{ $leaveRequest->first_date->format('d M Y') }}
        </td>

        <td class="label">Last Date</td>
        <td class="value">
            {{ $leaveRequest->last_date->format('d M Y') }}
        </td>
    </tr>

    <tr>
        <td class="label">Total Working Days</td>
        <td class="value">
            {{ $leaveRequest->total_days }} day(s)
        </td>

        <td class="label">Submitted Date</td>
        <td class="value">
            {{ $leaveRequest->created_at->format('d M Y H:i') }}
        </td>
    </tr>

    <tr>
        <td class="label">Reason</td>
        <td colspan="3">
            {{ $leaveRequest->reason }}
        </td>
    </tr>

</table>


{{-- APPROVAL & MONITORING --}}
<div class="section-title">
    APPROVAL &amp; MONITORING
</div>

<table class="approval-table">

    <thead>
        <tr>
            <th style="width: 33%;">
                Employee Request
            </th>

            <th style="width: 33%;">
                Manager Approval
            </th>

            <th style="width: 34%;">
                HRD Monitoring
            </th>
        </tr>
    </thead>

    <tbody>

        <tr>

            {{-- EMPLOYEE --}}
            <td>

                <strong>
                    {{ $leaveRequest->employee->full_name ?? '-' }}
                </strong>

                <div class="signature-space"></div>

                <strong>
                    Submitted
                </strong>

            </td>


            {{-- MANAGER --}}
            <td>

                <strong>
                    {{ $leaveRequest->manager->full_name ?? '-' }}
                </strong>

                <div class="signature-space"></div>

                @if($leaveRequest->status === 'approved')

                    <strong class="status-approved">
                        Approved
                    </strong>

                    @if($leaveRequest->manager_approved_at)
                        <br>
                        <small>
                            {{ $leaveRequest->manager_approved_at->format('d M Y H:i') }}
                        </small>
                    @endif

                @elseif($leaveRequest->status === 'rejected')

                    <strong class="status-rejected">
                        Rejected
                    </strong>

                @else

                    <strong class="status-pending">
                        Pending Approval
                    </strong>

                @endif

            </td>


            {{-- HRD --}}
            <td>

                <strong>
                    HRD
                </strong>

                <div class="signature-space"></div>

                <strong>
                    Monitoring Status
                </strong>

                <br>

                <small>
                    For HRD Monitoring
                </small>

            </td>

        </tr>

    </tbody>

</table>


{{-- MANAGER REJECTION REASON --}}
@if($leaveRequest->status === 'rejected' && $leaveRequest->manager_rejection_reason)

    <div class="rejection-reason">

        <div class="rejection-reason-title">
            Manager Rejection Reason
        </div>

        <div>
            {{ $leaveRequest->manager_rejection_reason }}
        </div>

    </div>

@endif


<div class="footer">
    EIMS - Leave Request Form
</div>


</body>
</html>
