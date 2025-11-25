<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JuryApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Congratulations! Your Jury Application has been Approved')
                    ->view('emails.jury-approved')
                    ->with([
                        'userName' => $this->registration->user->name,
                        'eventTitle' => $this->registration->event->title,
                        'eventDate' => $this->registration->event->start_date->format('F j, Y'),
                        'eventTime' => $this->registration->event->start_date->format('g:i A'),
                        'eventLocation' => $this->registration->event->location,
                        'organizerName' => $this->registration->event->organizer->name ?? 'Event Organizer',
                    ]);
    }
}
