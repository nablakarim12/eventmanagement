<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventOrganizerStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $status;
    protected $reason;

    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
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
        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Event Organizer Account Approved')
                ->markdown('emails.organizers.approved', [
                    'organizer' => $notifiable
                ]);
        }

        return (new MailMessage)
            ->subject('Event Organizer Application Update')
            ->markdown('emails.organizers.rejected', [
                'organizer' => $notifiable,
                'reason' => $this->reason
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
