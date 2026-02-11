<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Incident;

class CriticalIncidentOccurred extends Notification
{
    use Queueable;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function via(object $notifiable): array
    {
        // Check if user has opted out of critical alerts
        $settings = $notifiable->settings ?? [];
        if (isset($settings['email_critical']) && $settings['email_critical'] === false) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Critical Alert: ' . $this->incident->store->name)
            ->error() // Red color branding
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A critical health issue has been detected in your project.')
            ->line('**Issue:** ' . $this->incident->title)
            ->line('**Description:** ' . $this->incident->description)
            ->action('View Incident', route('incidents.show', $this->incident))
            ->line('Please investigate immediately.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
