<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PresentationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;
    public $averageScore;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, $averageScore, $reason)
    {
        $this->event = $event;
        $this->averageScore = $averageScore;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Paper Review Results - ' . $this->event->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for your submission to **' . $this->event->title . '**.')
            ->line('After careful review by our expert panel, we regret to inform you that your paper was **not selected for presentation** at this time.')
            ->line('**Event Details:**')
            ->line('📅 Event: ' . $this->event->title);

        if ($this->averageScore) {
            $mail->line('⭐ Your Average Score: ' . number_format($this->averageScore, 1) . '/100');
        }

        if ($this->reason) {
            $mail->line('**Reviewer Feedback:**')
                 ->line($this->reason);
        }

        $mail->line('We appreciate your interest and encourage you to consider submitting to future events.')
             ->line('This decision does not diminish the value of your work, and we hope you will continue your research efforts.')
             ->action('View My Dashboard', url('/dashboard'))
             ->line('Thank you for your understanding.')
             ->salutation('Best regards, ' . ($this->event->organizer->company_name ?? 'Event Management Team'));

        return $mail;
    }

    /**
     * Get the array representation of the notification (for database storage).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'presentation_rejected',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'average_score' => $this->averageScore,
            'rejection_reason' => $this->reason,
            'message' => 'Your paper submission for ' . $this->event->title . ' was not selected for presentation.',
        ];
    }
}

