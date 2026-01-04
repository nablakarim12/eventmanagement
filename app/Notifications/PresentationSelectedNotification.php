<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PresentationSelectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;
    public $registration;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, EventRegistration $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
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
        $deliveryMode = ucfirst(str_replace('_', ' ', $this->event->delivery_mode));
        
        $mail = (new MailMessage)
            ->subject('🎉 Congratulations! Your Presentation Has Been Selected - ' . $this->event->title)
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('We are pleased to inform you that your paper submission has been reviewed and **selected for presentation** at **' . $this->event->title . '**.')
            ->line('**Event Details:**')
            ->line('📅 Event: ' . $this->event->title)
            ->line('📍 Mode: ' . $deliveryMode);

        if ($this->registration->average_score) {
            $mail->line('⭐ Your Average Score: ' . $this->registration->average_score . '/100');
        }

        if ($this->registration->presentation_time) {
            $mail->line('🕒 Presentation Time: ' . \Carbon\Carbon::parse($this->registration->presentation_time)->format('F d, Y h:i A'));
        }

        if ($this->registration->presentation_queue) {
            $mail->line('🔢 Queue Number: #' . $this->registration->presentation_queue);
        }

        // Location or Link based on delivery mode
        if ($this->event->delivery_mode === 'online' || $this->event->delivery_mode === 'hybrid') {
            if ($this->registration->presentation_link) {
                $mail->line('🔗 Meeting Link: ' . $this->registration->presentation_link)
                     ->action('Join Online Meeting', $this->registration->presentation_link);
            }
        }

        if ($this->event->delivery_mode === 'face_to_face' || $this->event->delivery_mode === 'hybrid') {
            if ($this->registration->presentation_location) {
                $mail->line('📍 Location: ' . $this->registration->presentation_location);
            }
        }

        $mail->line('Please prepare your presentation and be ready at the scheduled time.')
             ->line('**Important:** Please arrive/log in at least 15 minutes before your scheduled presentation time.')
             ->action('View My Dashboard', url('/dashboard'))
             ->line('Thank you for your contribution to ' . $this->event->title . '!')
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
            'type' => 'presentation_selected',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'registration_id' => $this->registration->id,
            'presentation_queue' => $this->registration->presentation_queue,
            'presentation_time' => $this->registration->presentation_time,
            'presentation_link' => $this->registration->presentation_link,
            'presentation_location' => $this->registration->presentation_location,
            'average_score' => $this->registration->average_score,
            'delivery_mode' => $this->event->delivery_mode,
            'message' => 'Congratulations! Your presentation has been selected for ' . $this->event->title,
        ];
    }
}
