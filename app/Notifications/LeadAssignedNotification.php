<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('New Lead Assigned: ' . $this->lead->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been assigned a new lead.')
            ->line('Lead Details:')
            ->line('Name: ' . $this->lead->name)
            ->line('Email: ' . $this->lead->email)
            ->line('Phone: ' . ($this->lead->phone ?: 'Not provided'))
            ->line('Status: ' . ucfirst($this->lead->status))
            ->action('View Lead', url('/leads/' . $this->lead->id))
            ->line('Please contact this lead as soon as possible.')
            ->line('Thank you for your dedication!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'lead_email' => $this->lead->email,
            'message' => 'New lead assigned: ' . $this->lead->name,
        ];
    }
}
