<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QueueStatusNotification extends Notification
{
    use Queueable;

    protected $queueItem;
    protected $status;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($queueItem, $status, $message = null)
    {
        $this->queueItem = $queueItem;
        $this->status = $status;
        $this->message = $message ?? $this->getDefaultMessage($status);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'queue_update',
            'title' => $this->getTitle(),
            'message' => $this->message,
            'queue_id' => $this->queueItem->id,
            'queue_number' => $this->queueItem->queue_number,
            'status' => $this->status,
            'action_url' => route('patient.dashboard'),
        ];
    }

    /**
     * Get the notification title based on status.
     */
    protected function getTitle(): string
    {
        return match($this->status) {
            'approved' => '✅ Queue Request Approved',
            'Waiting' => '🎫 Your Queue Number is Ready',
            'Called' => '📢 You Have Been Called',
            'Serving' => '👨‍⚕️ Now Being Served',
            'Completed' => '✅ Consultation Completed',
            'rejected' => '❌ Queue Request Rejected',
            'Cancelled' => 'Queue Cancelled',
            default => 'Queue Update',
        };
    }

    /**
     * Get default message based on status.
     */
    protected function getDefaultMessage($status): string
    {
        return match($status) {
            'approved' => "Your queue request has been approved. Your queue number is {$this->queueItem->queue_number}. Please arrive 15 minutes before your scheduled time.",
            'Waiting' => "Your queue number {$this->queueItem->queue_number} has been assigned. You are currently in the waiting list.",
            'Called' => "Queue number {$this->queueItem->queue_number} has been called. Please proceed to the consultation area.",
            'Serving' => "You are now being served. Queue number: {$this->queueItem->queue_number}.",
            'Completed' => "Your consultation has been completed. Thank you for visiting Mabini Health Center.",
            'rejected' => "Your queue request has been rejected. Please contact the health center for more information.",
            'Cancelled' => "Queue number {$this->queueItem->queue_number} has been cancelled.",
            default => "Your queue status has been updated to: {$status}",
        };
    }
}