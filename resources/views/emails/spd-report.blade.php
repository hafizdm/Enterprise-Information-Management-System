<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPD Report Notification</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif; color:#273444;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f6f8; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border:1px solid #e2e8f0;">

                    <!-- RAPID HEADER -->
                    <tr>
                        <td style="padding:28px 32px; border-bottom:3px solid #1769aa;">
                            <img
                                src="{{ asset('images/company-logo.png') }}"
                                alt="RAPID"
                                width="240"
                                style="display:block; width:100%; max-width:240px; height:auto; border:0;"
                            >

                            <div style="margin-top:8px; font-size:13px; color:#64748b;">
                                Engineering Information Management System
                            </div>
                        </td>
                    </tr>

                    <!-- EMAIL BODY -->
                    <tr>
                        <td style="padding:28px 32px; font-size:14px; line-height:1.7;">

                            <h2 style="margin:0 0 20px; font-size:20px; color:#1e293b;">
                                {{ $isApprovalRequest ? 'SPD Report Approval Required' : 'SPD Report Approval Update' }}
                            </h2>

                            <p style="margin:0 0 20px;">
                                @if ($isApprovalRequest)
                                    Please review this SPD Report and confirm your decision in RAPID.
                                @else
                                    The approval status of this SPD Report has been updated.
                                @endif
                            </p>

                            <!-- REPORT DETAILS -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; font-size:14px;">

                                <tr>
                                    <td style="padding:9px 0; width:155px; color:#64748b; vertical-align:top;">
                                        SPD Number
                                    </td>
                                    <td style="padding:9px 0; font-weight:bold; color:#1e293b;">
                                        {{ $spdNumber }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Employee
                                    </td>
                                    <td style="padding:9px 0;">
                                        {{ $employeeName }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Project
                                    </td>
                                    <td style="padding:9px 0;">
                                        {{ $projectName }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Departure Date
                                    </td>
                                    <td style="padding:9px 0;">
                                        {{ $departureDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Return Date
                                    </td>
                                    <td style="padding:9px 0;">
                                        {{ $returnDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Total Days
                                    </td>
                                    <td style="padding:9px 0;">
                                        {{ $totalDays }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                        Report Status
                                    </td>
                                    <td style="padding:9px 0; font-weight:bold; color:#1e293b;">
                                        {{ $statusLabel }}
                                    </td>
                                </tr>

                                @if (!$isApprovalRequest && $result === 'rejected')
                                    <tr>
                                        <td style="padding:9px 0; color:#64748b; vertical-align:top;">
                                            Rejection Reason
                                        </td>
                                        <td style="padding:9px 0;">
                                            {{ $rejectionReason ?: '-' }}
                                        </td>
                                    </tr>
                                @endif

                            </table>

                            <!-- APPROVAL BUTTON -->
                            @if ($isApprovalRequest && $approvalUrl)
                                <div style="margin-top:24px; margin-bottom:28px;">
                                    <a
                                        href="{{ $approvalUrl }}"
                                        style="display:inline-block; padding:12px 22px; background-color:#1769aa; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; border-radius:4px;"
                                    >
                                        Review SPD Report in RAPID
                                    </a>
                                </div>
                            @endif

                            <!-- APPROVAL RESULT MESSAGE -->
                            @if (!$isApprovalRequest)
                                @if ($result === 'approved')
                                    <p style="margin:20px 0; color:#475569;">
                                        The SPD Report has been approved by the Manager.
                                    </p>
                                @elseif ($result === 'rejected')
                                    <p style="margin:20px 0; color:#475569;">
                                        The SPD Report has been rejected by the Manager.
                                        Please review the rejection reason and submit the corrected report through RAPID.
                                    </p>
                                @endif
                            @endif

                            <p style="margin:0; color:#475569;">
                                Regards,<br>
                                <strong>RAPID Notification</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="padding:16px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0; font-size:11px; line-height:1.6; color:#94a3b8;">
                            This is an automated notification from RAPID. Please do not reply to this email.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
