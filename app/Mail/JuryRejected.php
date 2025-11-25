<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JuryRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(EventRegistration $registration, $reason = null)
    {
        $this->registration = $registration;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Update on Your Jury Application')
                    ->view('emails.jury-rejected')
                    ->with([
                        'userName' => $this->registration->user->name,
                        'eventTitle' => $this->registration->event->title,
                        'reason' => $this->reason,
                        'organizerName' => $this->registration->event->organizer->name ?? 'Event Organizer',
                    ]);
    }
}
