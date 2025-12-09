<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeStaffNotification extends Notification
{
    use Queueable;

    protected $password;
    protected $role;
    protected $serviceName;

    /**
     * Create a new notification instance.
     */
    public function __construct($password, $role, $serviceName = null)
    {
        $this->password = $password;
        $this->role = $role;
        $this->serviceName = $serviceName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $roleDisplay = match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'staff' => 'Service Staff',
            'front_desk' => 'Front Desk Staff',
            default => ucfirst($this->role)
        };

        if ($this->role === 'staff' && $this->serviceName) {
            $roleDisplay .= ' - ' . $this->serviceName;
        }

        return (new MailMessage)
            ->subject('Welcome to Mabini Health Center - Account Created')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account has been created at Mabini Health Center Queue Management System.')
            ->line('**Role:** ' . $roleDisplay)
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Temporary Password:** ' . $this->password)
            ->line('Please use these credentials to log in to the system.')
            ->action('Login to System', url('/login'))
            ->line('**Important:** For security reasons, please change your password after your first login.')
            ->line('If you have any questions or need assistance, please contact your system administrator.')
            ->salutation('Best regards, Mabini Health Center Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'role' => $this->role,
        ];
    }
}
