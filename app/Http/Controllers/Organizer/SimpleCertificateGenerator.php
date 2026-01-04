<?php

namespace App\Http\Controllers\Organizer;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\CertificateTemplate;
use App\Models\GeneratedCertificate;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SimpleCertificateGenerator
{
    const MM_TO_PX = 11.81;

    public static function generate(Event $event, EventRegistration $registration, CertificateTemplate $template, string $type, string $role)
    {
        // Check if already generated
        $existing = GeneratedCertificate::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->where('certificate_type', $type)
            ->first();

        if ($existing) {
            return false;
        }

        try {
            // Prepare text data
            $name = $registration->user->name;
            $eventName = $event->title;
            $eventDate = $event->start_date->format('F d, Y');

            // Save to database - certificate will be generated as HTML on demand
            $certificate = GeneratedCertificate::create([
                'event_id' => $event->id,
                'user_id' => $registration->user_id,
                'registration_id' => $registration->id,
                'template_id' => $template->id,
                'certificate_url' => route('certificates.view', ['certificate' => 'PLACEHOLDER']), // Will be updated after creation
                'cloudinary_public_id' => null,
                'certificate_type' => $type,
                'participant_name' => $name,
                'participant_role' => $role,
                'event_name' => $eventName,
                'event_date' => $eventDate,
                'generated_at' => now(),
            ]);

            // Update with actual URL
            $certificate->update([
                'certificate_url' => route('certificates.view', ['certificate' => $certificate->id])
            ]);

            \Log::info("Certificate created with ID: {$certificate->id}");

            return true;
        } catch (\Exception $e) {
            \Log::error("Certificate generation error: " . $e->getMessage());
            throw $e;
        }
    }
}
