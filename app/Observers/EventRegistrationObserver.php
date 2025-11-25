<?php

namespace App\Observers;

use App\Models\EventRegistration;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventRegistrationObserver
{
    /**
     * Handle the EventRegistration "created" event.
     */
    public function created(EventRegistration $eventRegistration): void
    {
        //
    }

    /**
     * Handle the EventRegistration "updated" event.
     */
    public function updated(EventRegistration $eventRegistration): void
    {
        // Generate QR code when registration is approved
        if ($eventRegistration->wasChanged('approved_at') && $eventRegistration->approved_at !== null) {
            $this->generateQrCode($eventRegistration);
        }
    }

    /**
     * Handle the EventRegistration "deleted" event.
     */
    public function deleted(EventRegistration $eventRegistration): void
    {
        // Delete QR code image when registration is deleted
        if ($eventRegistration->qr_image_path) {
            Storage::disk('public')->delete($eventRegistration->qr_image_path);
        }
    }

    /**
     * Handle the EventRegistration "restored" event.
     */
    public function restored(EventRegistration $eventRegistration): void
    {
        //
    }

    /**
     * Handle the EventRegistration "force deleted" event.
     */
    public function forceDeleted(EventRegistration $eventRegistration): void
    {
        //
    }

    /**
     * Generate QR code for the registration
     */
    protected function generateQrCode(EventRegistration $registration): void
    {
        // Generate unique QR code identifier
        $qrCode = 'REG-' . strtoupper(Str::random(12));
        
        // Create the QR code URL - this will be scanned to check in
        $checkInUrl = route('qr.scan.registration', ['qrCode' => $qrCode]);
        
        // Generate QR code image
        $qrCodeObj = new QrCode($checkInUrl);
        $writer = new PngWriter();
        $result = $writer->write($qrCodeObj);
        
        // Save QR code image
        $filename = 'qr-codes/' . $qrCode . '.png';
        Storage::disk('public')->put($filename, $result->getString());
        
        // Update registration with QR code data without triggering events
        $registration::withoutEvents(function () use ($registration, $qrCode, $filename) {
            $registration->qr_code = $qrCode;
            $registration->qr_image_path = $filename;
            $registration->save();
        });
    }
}
