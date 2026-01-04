<?php

namespace App\Observers;

use App\Models\EventRegistration;
use App\Services\CloudinaryService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        // Delete QR code from Cloudinary when registration is deleted
        if ($eventRegistration->qr_code_public_id) {
            try {
                $cloudinaryService = app(CloudinaryService::class);
                $cloudinaryService->delete($eventRegistration->qr_code_public_id, 'image');
                Log::info('QR code deleted from Cloudinary for registration: ' . $eventRegistration->id);
            } catch (\Exception $e) {
                Log::error('Failed to delete QR code from Cloudinary: ' . $e->getMessage());
            }
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
        try {
            // Generate unique QR code identifier
            $qrCode = 'REG-' . strtoupper(Str::random(12));
            
            // Create the QR code URL - this will be scanned to check in
            $checkInUrl = route('qr.scan.registration', ['qrCode' => $qrCode]);
            
            // Generate QR code image
            $qrCodeObj = new QrCode($checkInUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCodeObj);
            
            // Save temporarily
            $tempPath = storage_path('app/temp/qr_' . $qrCode . '.png');
            
            // Create temp directory if it doesn't exist
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }
            
            $result->saveToFile($tempPath);
            
            // Upload to Cloudinary
            $cloudinaryService = app(CloudinaryService::class);
            $uploadResult = $cloudinaryService->uploadQrCode(
                $tempPath,
                'qr_registration_' . $registration->id . '.png',
                'qr-codes/registrations'
            );
            
            // Delete old QR code from Cloudinary if exists
            if ($registration->qr_code_public_id) {
                $cloudinaryService->delete($registration->qr_code_public_id, 'image');
            }
            
            // Update registration with QR code data without triggering events
            $registration::withoutEvents(function () use ($registration, $qrCode, $uploadResult) {
                $registration->qr_code = $qrCode;
                $registration->qr_image_path = $uploadResult['secure_url'];
                $registration->qr_code_public_id = $uploadResult['public_id'];
                $registration->save();
            });
            
            // Delete temporary file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            Log::info('QR code generated and uploaded to Cloudinary for registration: ' . $registration->id);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code for registration ' . $registration->id . ': ' . $e->getMessage());
        }
    }
}
