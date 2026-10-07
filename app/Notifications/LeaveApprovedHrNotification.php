<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApprovedHrNotification extends Notification implements ShouldQueue
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
        /*
         * Load relationship yang dibutuhkan oleh
         * email dan PDF.
         */
        $leaveRequest = $this->leaveRequest->load([
            'employee.division',
            'employee.position',
            'employee.project',
            'manager',
        ]);

        $employee = $leaveRequest->employee;
        $manager = $leaveRequest->manager;

        /*
         * Generate PDF berdasarkan Leave Request
         * setelah Manager melakukan approval.
         *
         * Status pada PDF akan menjadi APPROVED.
         */
        $pdf = Pdf::loadView(
            'hr.leave_requests.pdf',
            compact('leaveRequest')
        );

        return (new MailMessage)
            ->subject(
                '[EIMS] Leave Request - ' .
                $employee->full_name .
                ' - Approved by Manager'
            )
            ->greeting('Dear HR Team,')
            ->line(
                'A leave request has been approved by the Manager.'
            )
            ->line(
                'This notification is for HR monitoring and record purposes. No further approval is required from HR.'
            )
            ->line(
                'Employee: ' . $employee->full_name
            )
            ->line(
                'Manager: ' . ($manager?->full_name ?? '-')
            )
            ->line(
                'Leave Type: ' . ucfirst($leaveRequest->leave_type)
            )
            ->line(
                'Leave Period: ' .
                $leaveRequest->first_date->format('d M Y') .
                ' - ' .
                $leaveRequest->last_date->format('d M Y')
            )
            ->line(
                'Total Days: ' . $leaveRequest->total_days
            )
            ->line(
                'Reason: ' . ($leaveRequest->reason ?: '-')
            )
            ->line(
                'Status: APPROVED'
            )
            ->line(
                'Please keep the attached document for HR monitoring and record purposes.'
            )
            ->attachData(
                $pdf->output(),
                'leave-request-' . $leaveRequest->id . '-approved.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            )
            ->line('Regards,')
            ->line('EIMS Notification')
            ->salutation('');
    }
}