<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApprovedEmployeeNotification extends Notification implements ShouldQueue
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

        /*
         * Generate PDF berdasarkan data Leave Request
         * TERBARU.
         *
         * Notification ini dipanggil setelah status
         * Leave Request berubah menjadi "approved".
         *
         * Jadi PDF akan menampilkan status APPROVED.
         */
        $pdf = Pdf::loadView(
            'hr.leave_requests.pdf',
            compact('leaveRequest')
        );

        return (new MailMessage)
            ->subject(
                '[EIMS] Leave Request - ' .
                $employee->full_name .
                ' - Approved'
            )
            ->greeting('Dear ' . $employee->full_name . ',')
            ->line(
                'Your leave request has been approved by your Manager.'
            )
            ->line(
                'Employee: ' . $employee->full_name
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
                'Please keep this email and the attached document for your records.'
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