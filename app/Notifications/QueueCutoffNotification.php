<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Queue;

class QueueCutoffNotification extends Notification
{
    use Queueable;

    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Health Center Cutoff - Queue Not Attended')
            ->greeting('Hello ' . ($this->queue->patient->full_name ?? 'Patient') . ',')
            ->line('Unfortunately, the health center has reached its cutoff time for today.')
            ->line('Your queue request #' . ($this->queue->queue_number ?? '') . ' could not be attended to.')
            ->line('')
            ->line('**You Have 2 Options for Tomorrow:**')
            ->line('1️⃣ **Request Priority Queue** (Recommended) - You\'ll be served first as a courtesy for today\'s inconvenience')
            ->line('2️⃣ **Request Regular Queue** - Join the normal queue like other patients')
            ->line('')
            ->line('**Priority Queue Benefits:**')
            ->line('• Skip ahead of regular patients')
            ->line('• Valid for tomorrow only')
            ->line('• One-time courtesy')
            ->line('')
            ->line('**Tomorrow\'s Operating Hours:** We open at 8:00 AM')
            ->line('We apologize for the inconvenience. Please arrive early to secure your priority status.')
            ->action('Request Priority Queue for Tomorrow', url('/patient/queue/request-priority?from_cutoff=' . ($this->queue->id ?? '')))
            ->line('Or click here to request a regular queue: ' . url('/patient/queue/request'))
            ->line('Thank you for your understanding!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $queueNumber = $this->queue->queue_number ?? '';
        
        return [
            'type' => 'queue_cutoff',
            'queue_id' => $this->queue->id ?? null,
            'queue_number' => $queueNumber,
            'title' => 'Queue Not Attended - Priority Option Available',
            'message' => "Your queue #{$queueNumber} was not attended due to cutoff. Request PRIORITY queue tomorrow as a courtesy!",
            'action_url' => url('/patient/queue/request-priority?from_cutoff=' . ($this->queue->id ?? '')),
            'action_text' => 'Request Priority Queue',
            'priority_available' => true,
        ];
    }
}
