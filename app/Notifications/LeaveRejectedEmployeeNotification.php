<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRejectedEmployeeNotification extends Notification implements ShouldQueue
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
         * Generate PDF berdasarkan data Leave Request
         * TERBARU.
         *
         * Karena notification dipanggil setelah status
         * berubah menjadi rejected, PDF akan menampilkan
         * status REJECTED.
         */
        $pdf = Pdf::loadView(
            'hr.leave_requests.pdf',
            compact('leaveRequest')
        );

        return (new MailMessage)
            ->subject(
                '[EIMS] Leave Request - ' .
                $employee->full_name .
                ' - Rejected'
            )
            ->greeting('Dear ' . $employee->full_name . ',')
            ->line(
                'Your leave request has been rejected by your Manager.'
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
                'Status: REJECTED'
            )
            ->line(
                'Rejection Reason: ' .
                ($leaveRequest->manager_rejection_reason ?: '-')
            )
            ->line(
                'Please review the rejection reason and contact your Manager if further clarification is required.'
            )
            ->attachData(
                $pdf->output(),
                'leave-request-' . $leaveRequest->id . '-rejected.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            )
            ->line('Regards,')
            ->line('EIMS Notification')
            ->salutation('');
    }
}