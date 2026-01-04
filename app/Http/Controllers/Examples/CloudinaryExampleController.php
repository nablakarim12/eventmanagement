<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use App\Models\Event;
use Illuminate\Http\Request;

/**
 * Example controller showing how to use Cloudinary for file uploads
 * This demonstrates the pattern you should follow in your existing controllers
 */
class CloudinaryExampleController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Example 1: Upload event poster/featured image
     */
    public function uploadEventPoster(Request $request, Event $event)
    {
        $request->validate([
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB
        ]);

        try {
            // Upload to Cloudinary
            $uploadResult = $this->cloudinaryService->uploadImage(
                $request->file('featured_image'),
                'events/posters', // Folder in Cloudinary
                [
                    'public_id' => 'event_' . $event->id . '_poster',
                    'overwrite' => true,
                    'transformation' => [
                        'width' => 1920,
                        'height' => 1080,
                        'crop' => 'limit',
                        'quality' => 'auto:good'
                    ]
                ]
            );

            // Delete old image if exists
            if ($event->featured_image_public_id) {
                $this->cloudinaryService->delete($event->featured_image_public_id);
            }

            // Save the URL and public_id to database
            $event->update([
                'featured_image' => $uploadResult['secure_url'],
                'featured_image_public_id' => $uploadResult['public_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'url' => $uploadResult['secure_url']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Example 2: Upload PDF document (e.g., paper submission)
     */
    public function uploadPaperDocument(Request $request)
    {
        $request->validate([
            'paper_file' => 'required|file|mimes:pdf|max:10240' // 10MB
        ]);

        try {
            $uploadResult = $this->cloudinaryService->uploadPdf(
                $request->file('paper_file'),
                'papers/' . date('Y/m'), // Organize by year/month
                [
                    'public_id' => 'paper_' . uniqid(),
                ]
            );

            // Save to your database
            // PaperSubmission::create([...]);

            return response()->json([
                'success' => true,
                'url' => $uploadResult['secure_url'],
                'public_id' => $uploadResult['public_id']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Example 3: Upload QR Code
     */
    public function uploadQrCode($registrationId, $qrCodePath)
    {
        try {
            $uploadResult = $this->cloudinaryService->uploadQrCode(
                $qrCodePath,
                'qr_registration_' . $registrationId . '.png',
                'qr-codes/registrations'
            );

            // Save QR code URL to registration record
            // EventRegistration::find($registrationId)->update([
            //     'qr_code_url' => $uploadResult['secure_url'],
            //     'qr_code_public_id' => $uploadResult['public_id']
            // ]);

            // Clean up temporary local file
            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }

            return $uploadResult;

        } catch (\Exception $e) {
            \Log::error('QR Code upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Example 4: Upload multiple files (e.g., jury documents)
     */
    public function uploadMultipleDocuments(Request $request)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        try {
            $files = $request->file('documents');
            $uploadResults = $this->cloudinaryService->uploadMultiple(
                $files,
                'jury/qualifications',
                'pdf'
            );

            return response()->json([
                'success' => true,
                'files' => $uploadResults
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Example 5: Get optimized thumbnail
     */
    public function getEventThumbnail(Event $event)
    {
        if (!$event->featured_image_public_id) {
            return null;
        }

        // Generate thumbnail URL on-the-fly (no storage needed!)
        $thumbnailUrl = $this->cloudinaryService->getThumbnail(
            $event->featured_image_public_id,
            300,
            300
        );

        return $thumbnailUrl;
    }

    /**
     * Example 6: Delete an image
     */
    public function deleteEventImage(Event $event)
    {
        try {
            if ($event->featured_image_public_id) {
                $deleted = $this->cloudinaryService->delete(
                    $event->featured_image_public_id,
                    'image'
                );

                if ($deleted) {
                    $event->update([
                        'featured_image' => null,
                        'featured_image_public_id' => null
                    ]);

                    return response()->json(['success' => true]);
                }
            }

            return response()->json(['success' => false, 'message' => 'No image to delete']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
