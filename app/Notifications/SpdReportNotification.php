<?php

namespace App\Notifications;

use App\Models\SpdReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;


class SpdReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected SpdReport $spdReport;

    protected string $notificationType;

    public function __construct(
        SpdReport $spdReport,
        string $notificationType = 'result'
    ) {
        $this->spdReport = $spdReport;
        $this->notificationType = $notificationType;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {

        $this->spdReport->loadMissing([
            'employee.division',
            'employee.position',
            'employee.costLevel',
            'spd.project',
            'spd.manager.position',
            'spd.approvalDocument.position',
            'spd.creator.employee.position',
        ]);


        $report = $this->spdReport;
        $spd = $report->spd;

        $isApprovalRequest = $this->notificationType === 'request';
        $result = $report->status_report;

        $employeeName = $report->employee?->full_name ?? '-';
        $spdNumber = $spd?->spd_number ?? (string) $report->spd_id;
        $projectName = $spd?->project?->name ?? '-';

        $departureDate = $report->date_departure?->format('d M Y') ?? '-';
        $returnDate = $report->date_return?->format('d M Y') ?? '-';
        $totalDays = $report->total_days ?? '-';

        $statusLabel = match ($result) {
            'approved' => 'APPROVED',
            'rejected' => 'REJECTED',
            'submitted' => 'SUBMITTED',
            default => strtoupper((string) $result),
        };

        $rejectionReason = $report->manager_rejection_reason;

        $approvalUrl = null;

        if ($isApprovalRequest) {
            $approvalUrl = URL::temporarySignedRoute(
                'spd-report-approvals.email-confirm',
                now()->addDays(7),
                [
                    'spdReport' => $report->id,
                ]
            );
        }

        $subject = $isApprovalRequest
            ? '[RAPID] SPD Report - Approval Required - ' . $spdNumber
            : '[RAPID] SPD Report - ' . strtoupper($result) . ' - ' . $spdNumber;


        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'hr.spd-reports.pdf',
            ['spdReport' => $report]
        );

        $mail = (new MailMessage)
            ->subject($subject)
            ->view('emails.spd-report', [
                'isApprovalRequest' => $isApprovalRequest,
                'approvalUrl' => $approvalUrl,
                'spdNumber' => $spdNumber,
                'employeeName' => $employeeName,
                'projectName' => $projectName,
                'departureDate' => $departureDate,
                'returnDate' => $returnDate,
                'totalDays' => $totalDays,
                'statusLabel' => $statusLabel,
                'result' => $result,
                'rejectionReason' => $rejectionReason,
            ])
            ->attachData(
                $pdf->output(),
                'spd-report-' . $report->id . '.pdf',
                ['mime' => 'application/pdf']
            );

        if (
            $report->expense_evidence &&
            Storage::disk('public')->exists($report->expense_evidence)
        ) {
            $mail->attachFromStorageDisk(
                'public',
                $report->expense_evidence,
                basename($report->expense_evidence)
            );
        }

        return $mail;

    }

    public function toArray(object $notifiable): array
    {
        return [
            'spd_report_id' => $this->spdReport->id,
            'spd_id' => $this->spdReport->spd_id,
            'status_report' => $this->spdReport->status_report,
            'notification_type' => $this->notificationType,
        ];
    }
}
