<?php

namespace App\Notifications;

use App\Models\Spd;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class SpdApprovalResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Spd $spd;
    protected string $result;
    protected string $approvalStage;
    protected string $notificationType;

    public function __construct(
        Spd $spd,
        string $result,
        string $approvalStage,
        string $notificationType = 'result'
    ) {
        $this->spd = $spd;
        $this->result = $result;
        $this->approvalStage = $approvalStage;
        $this->notificationType = $notificationType;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        $this->spd->loadMissing([
            'employee',
            'project',
            'creator',
        ]);

        $stageLabel = $this->approvalStage === 'cost_control'
            ? 'Cost Control'
            : 'Manager';

        $isApprovalRequest = $this->notificationType === 'request';

        $statusLabel = $this->result === 'approved'
            ? 'Approved'
            : 'Rejected';

        $subject = $isApprovalRequest
            ? sprintf(
                'SPD %s - Approval Required (%s)',
                $this->spd->spd_number,
                $stageLabel
            )
            : sprintf(
                'SPD %s - %s',
                $this->spd->spd_number,
                $statusLabel
            );

        $approvalUrl = null;

        if ($isApprovalRequest) {
            $approvalUrl = URL::temporarySignedRoute(
                'spd.approvals.email-confirm',
                now()->addDays(7),
                [
                    'spd' => $this->spd->id,
                    'stage' => $this->approvalStage,
                ]
            );
        }

        $rejectionReason = null;

        if (!$isApprovalRequest && $this->result === 'rejected') {
            $rejectionReason = $this->approvalStage === 'manager'
                ? $this->spd->manager_rejection_reason
                : $this->spd->document_rejection_reason;
        }

        $html = View::make('emails.spd-approval', [
            'spd' => $this->spd,
            'stageLabel' => $stageLabel,
            'isApprovalRequest' => $isApprovalRequest,
            'statusLabel' => $statusLabel,
            'result' => $this->result,
            'approvalUrl' => $approvalUrl,
            'rejectionReason' => $rejectionReason,
            'departureDate' => $this->spd->date_departure?->format('d-m-Y') ?? '-',
            'returnDate' => $this->spd->date_return?->format('d-m-Y') ?? '-',
        ])->render();

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.spd-approval', [
                'spd' => $this->spd,
                'stageLabel' => $stageLabel,
                'isApprovalRequest' => $isApprovalRequest,
                'statusLabel' => $statusLabel,
                'result' => $this->result,
                'approvalUrl' => $approvalUrl,
                'rejectionReason' => $rejectionReason,
                'departureDate' => $this->spd->date_departure?->format('d-m-Y') ?? '-',
                'returnDate' => $this->spd->date_return?->format('d-m-Y') ?? '-',
            ]);
    }
    
    public function toArray(object $notifiable): array
    {
        return [
            'spd_id' => $this->spd->id,
            'spd_number' => $this->spd->spd_number,
            'result' => $this->result,
            'approval_stage' => $this->approvalStage,
            'notification_type' => $this->notificationType,
        ];
    }
}