<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorAssignedNotification extends Notification
{
    use Queueable;

    public $queueItem;

    /**
     * Create a new notification instance.
     */
    public function __construct($queueItem)
    {
        $this->queueItem = $queueItem;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'doctor_assignment',
            'title' => '👨‍⚕️ New Patient Assigned',
            'message' => "You have been assigned to {$this->queueItem->patient->full_name} (Queue #{$this->queueItem->queue_number})",
            'queue_id' => $this->queueItem->id,
            'queue_number' => $this->queueItem->queue_number,
            'patient_id' => $this->queueItem->patient_id,
            'patient_name' => $this->queueItem->patient->full_name,
            'service_type' => $this->queueItem->service_type,
            'priority' => $this->queueItem->priority,
            'action_url' => route('doctor.my-queue')
        ];
    }
}
