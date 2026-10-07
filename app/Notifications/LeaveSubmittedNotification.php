<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class LeaveSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LeaveRequest $leaveRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $leaveRequest = $this->leaveRequest->load([
            'employee.division',
            'employee.position',
            'employee.project',
            'manager',
        ]);

        $employee = $leaveRequest->employee;

        $managerName = $notifiable->employee?->full_name
            ?? 'Manager';

        $pdf = Pdf::loadView(
            'hr.leave_requests.pdf',
            compact('leaveRequest')
        );

        /*
         * Signed URL for direct approval from email.
         * The manager ID is included in the signed URL so
         * the URL cannot be modified for another manager.
         */
        $approveUrl = URL::temporarySignedRoute(
            'leave-approvals.email-approve',
            now()->addHours(24),
            [
                'leaveRequest' => $leaveRequest->id,
                'manager' => $leaveRequest->manager_id,
            ]
        );

        /*
         * Signed URL for opening the leave approval page.
         * The manager must login before accessing the page.
         */
        $rejectUrl = URL::temporarySignedRoute(
            'leave-approvals.show',
            now()->addHours(24),
            [
                'leaveRequest' => $leaveRequest->id,
                'manager' => $leaveRequest->manager_id,
            ]
        );

        /*
         * Custom HTML buttons.
         *
         * MailMessage::action() only supports one action button.
         * Therefore, two custom buttons are rendered inside
         * an HtmlString.
         */
        $approvalButtons = new HtmlString(
            '<div style="text-align:center; margin:25px 0;">'

            . '<a href="' . e($approveUrl) . '" '
            . 'style="display:inline-block; '
            . 'padding:12px 22px; '
            . 'background-color:#198754; '
            . 'color:#ffffff; '
            . 'text-decoration:none; '
            . 'border-radius:6px; '
            . 'font-weight:bold; '
            . 'margin-right:8px;">'
            . 'APPROVE'
            . '</a>'

            . '<a href="' . e($rejectUrl) . '" '
            . 'style="display:inline-block; '
            . 'padding:12px 22px; '
            . 'background-color:#dc3545; '
            . 'color:#ffffff; '
            . 'text-decoration:none; '
            . 'border-radius:6px; '
            . 'font-weight:bold; '
            . 'margin-left:8px;">'
            . 'REJECT'
            . '</a>'

            . '</div>'
        );

        return (new MailMessage)
            ->subject(
                '[EIMS] Leave Request - ' .
                $employee->full_name .
                ' - Waiting Approval Manager'
            )
            ->greeting('Dear ' . $managerName . ',')
            ->line(
                'A new leave request has been submitted and is waiting for your approval.'
            )
            ->line('Employee: ' . $employee->full_name)
            ->line('Leave Type: ' . ucfirst($leaveRequest->leave_type))
            ->line(
                'Leave Period: ' .
                $leaveRequest->first_date->format('d M Y') .
                ' - ' .
                $leaveRequest->last_date->format('d M Y')
            )
            ->line('Total Days: ' . $leaveRequest->total_days)
            ->line('Reason: ' . ($leaveRequest->reason ?: '-'))
            ->line($approvalButtons)
            ->line(
                'Please review the attached Leave Request PDF before making your decision.'
            )
            ->attachData(
                $pdf->output(),
                'leave-request-' . $leaveRequest->id . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            )
            ->line('Regards,')
            ->line('EIMS Notification')
            ->salutation('');
    }
}
