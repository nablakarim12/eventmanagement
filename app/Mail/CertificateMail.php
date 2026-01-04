<?php

namespace App\Mail;

use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct(EventRegistration $registration, Event $event)
    {
        $this->registration = $registration;
        $this->event = $event;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Certificate is now a Cloudinary URL, download it temporarily to attach
        $certificateUrl = $this->registration->certificate_path;
        
        // Download file from Cloudinary
        $fileContent = @file_get_contents($certificateUrl);
        
        if ($fileContent === false) {
            throw new \Exception('Failed to download certificate from Cloudinary');
        }
        
        // Determine file extension from URL
        $isPdf = str_contains(strtolower($certificateUrl), '.pdf') || str_contains(strtolower($certificateUrl), '/raw/');
        $extension = $isPdf ? 'pdf' : 'jpg';
        $mimeType = $isPdf ? 'application/pdf' : 'image/jpeg';
        
        // Create temporary file
        $tempPath = sys_get_temp_dir() . '/' . 'cert_' . $this->registration->id . '_' . time() . '.' . $extension;
        file_put_contents($tempPath, $fileContent);
        
        $mail = $this->subject('Your Certificate for ' . $this->event->title)
                     ->view('emails.certificate')
                     ->attach($tempPath, [
                         'as' => 'Certificate_' . str_replace(' ', '_', $this->registration->user->name) . '.' . $extension,
                         'mime' => $mimeType,
                     ]);
        
        // Delete temp file after sending
        register_shutdown_function(function() use ($tempPath) {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        });
        
        return $mail;
    }
}

