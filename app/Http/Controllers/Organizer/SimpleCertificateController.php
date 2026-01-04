<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Mail\CertificateMail;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SimpleCertificateController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Show certificate upload page (simplified system)
     */
    public function showTemplateBuilder(Event $event)
    {
        // Determine event type by category
        $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';

        return view('organizer.simple-certificates.builder', compact('event', 'eventType'));
    }

    /**
     * Upload certificate for a specific participant/jury
     */
    public function uploadCertificate(Request $request, EventRegistration $registration)
    {
        $validated = $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        try {
            if ($request->hasFile('certificate') && $request->file('certificate')->isValid()) {
                $file = $request->file('certificate');
                
                // Determine file type
                $extension = strtolower($file->getClientOriginalExtension());
                $isPdf = ($extension === 'pdf');
                
                // Upload to Cloudinary
                if ($isPdf) {
                    // Upload PDF as raw file
                    $uploadResult = $this->cloudinaryService->uploadPdf(
                        $file,
                        'certificates',
                        [
                            'public_id' => 'cert_' . $registration->id . '_' . time(),
                            'resource_type' => 'raw'
                        ]
                    );
                } else {
                    // Upload image
                    $uploadResult = $this->cloudinaryService->uploadImage(
                        $file,
                        'certificates',
                        [
                            'public_id' => 'cert_' . $registration->id . '_' . time(),
                            'transformation' => [
                                'quality' => 'auto:good'
                            ]
                        ]
                    );
                }
                
                // Store Cloudinary URL and public_id
                $registration->certificate_path = $uploadResult['secure_url'];
                $registration->certificate_cloudinary_id = $uploadResult['public_id'];
                $registration->save();

                Log::info("Certificate uploaded to Cloudinary for registration #{$registration->id}: " . $uploadResult['secure_url']);

                return redirect()->back()->with('success', 'Certificate uploaded successfully for ' . $registration->user->name);
            }

            return redirect()->back()->with('error', 'Failed to upload certificate. Please ensure the file is valid.');
            
        } catch (\Exception $e) {
            Log::error("Certificate upload failed for registration #{$registration->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Send all certificates via email
     */
    public function sendAllCertificates(Event $event)
    {
        // Determine event type
        $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';

        // Get eligible registrations based on event type
        if ($eventType === 'innovation') {
            $registrations = EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->whereNotNull('certificate_path')
                ->get();
        } else {
            $registrations = EventRegistration::where('event_id', $event->id)
                ->whereNotNull('checked_in_at')
                ->whereNotNull('certificate_path')
                ->get();
        }

        if ($registrations->isEmpty()) {
            return redirect()->back()->with('error', 'No certificates to send.');
        }

        $sentCount = 0;
        foreach ($registrations as $registration) {
            try {
                // Send email with certificate attachment
                Mail::to($registration->user->email)->send(new CertificateMail($registration, $event));
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send certificate email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Successfully sent $sentCount certificates via email!");
    }

    /**
     * View attendees and their certificates (redirects to builder page)
     */
    public function showAttendees(Event $event)
    {
        // Redirect to the main builder/management page
        return redirect()->route('organizer.simple-certificates.builder', $event);
    }

    /**
     * List all events with certificate management
     */
    public function index(Request $request)
    {
        $eventType = $request->query('type', 'all');
        
        $query = Event::where('organizer_id', auth()->id())
            ->with(['category']);

        if ($eventType === 'innovation') {
            $query->whereHas('category', function($q) {
                $q->where('name', 'Innovation Competition');
            });
        } elseif ($eventType === 'conference') {
            $query->whereHas('category', function($q) {
                $q->where('name', '!=', 'Innovation Competition');
            });
        }

        $events = $query->latest()->get()->map(function($event) {
            $eventTypeCheck = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';
            
            if ($eventTypeCheck === 'innovation') {
                $event->eligible_count = EventRegistration::where('event_id', $event->id)
                    ->where('status', 'confirmed')
                    ->count();
                $event->uploaded_count = EventRegistration::where('event_id', $event->id)
                    ->where('status', 'confirmed')
                    ->whereNotNull('certificate_path')
                    ->count();
            } else {
                $event->eligible_count = EventRegistration::where('event_id', $event->id)
                    ->whereNotNull('checked_in_at')
                    ->count();
                $event->uploaded_count = EventRegistration::where('event_id', $event->id)
                    ->whereNotNull('checked_in_at')
                    ->whereNotNull('certificate_path')
                    ->count();
            }
            
            return $event;
        });

        return view('organizer.simple-certificates.index', compact('events', 'eventType'));
    }
}
