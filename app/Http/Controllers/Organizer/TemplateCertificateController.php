<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\CertificateTemplate;
use App\Models\GeneratedCertificate;
use App\Models\EventRegistration;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TemplateCertificateController extends Controller
{
    // Conversion constant: 1mm = 11.81 pixels at 300 DPI for A4
    const MM_TO_PX = 11.81;

    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    // Main certificate page - shows all events with attendance
    public function list()
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get conference events with checked-in attendees
        $events = Event::where('organizer_id', $organizer->id)
            ->whereNotNull('delivery_mode') // Conference events only
            ->where('status', 'published')
            ->with(['certificateTemplates'])
            ->get()
            ->map(function($event) {
                $event->checked_in_count = EventRegistration::where('event_id', $event->id)
                    ->whereNotNull('checked_in_at')
                    ->count();
                $event->total_registrations = EventRegistration::where('event_id', $event->id)->count();
                return $event;
            })
            ->filter(function($event) {
                return $event->checked_in_count > 0;
            })
            ->sortByDesc('start_date');

        return view('organizer.template-certificates.list', compact('events'));
    }

    // View eligible attendees for an event
    public function viewAttendees(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all checked-in registrations
        $attendees = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('checked_in_at')
            ->with(['user', 'juryMappingsAsReviewer', 'generatedCertificates'])
            ->get()
            ->map(function($registration) {
                $isApprovedPresenter = $registration->presentation_status === 'selected';
                $isReviewer = $registration->juryMappingsAsReviewer->count() > 0;
                
                if ($isApprovedPresenter && $isReviewer) {
                    $registration->certificate_role = 'Both';
                    $registration->role_badge = 'indigo';
                } elseif ($isReviewer) {
                    $registration->certificate_role = 'Reviewer';
                    $registration->role_badge = 'purple';
                } else {
                    $registration->certificate_role = 'Participant';
                    $registration->role_badge = 'blue';
                }
                
                return $registration;
            });

        $stats = [
            'total_attendees' => $attendees->count(),
            'participants' => $attendees->where('certificate_role', 'Participant')->count(),
            'reviewers' => $attendees->where('certificate_role', 'Reviewer')->count(),
            'both' => $attendees->where('certificate_role', 'Both')->count(),
            'generated_certificates' => GeneratedCertificate::where('event_id', $event->id)->count(),
        ];

        return view('organizer.template-certificates.attendees', compact('event', 'attendees', 'stats'));
    }

    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $templates = CertificateTemplate::where('event_id', $event->id)->get();
        $participantTemplate = $templates->where('template_type', 'participant')->first();
        $reviewerTemplate = $templates->where('template_type', 'reviewer')->first();

        $generatedCertificates = GeneratedCertificate::where('event_id', $event->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        // Count eligible attendees
        $checkedInCount = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('checked_in_at')
            ->count();

        $stats = [
            'total_generated' => GeneratedCertificate::where('event_id', $event->id)->count(),
            'checked_in' => $checkedInCount,
            'participant_template' => $participantTemplate ? 'Uploaded' : 'Not uploaded',
            'reviewer_template' => $reviewerTemplate ? 'Uploaded' : 'Not uploaded',
        ];

        return view('organizer.template-certificates.index', compact('event', 'participantTemplate', 'reviewerTemplate', 'generatedCertificates', 'stats'));
    }

    public function showUploadForm(Event $event, $type = null)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get eligible attendees count
        $attendeesCount = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('checked_in_at')
            ->count();

        $participantTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'participant')
            ->first();

        $reviewerTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'reviewer')
            ->first();

        return view('organizer.template-certificates.upload-new', compact('event', 'attendeesCount', 'participantTemplate', 'reviewerTemplate'));
    }

    public function uploadTemplate(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'participant_template' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'reviewer_template' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'name_x' => 'required|numeric|min:0|max:210',
            'name_y' => 'required|numeric|min:0|max:297',
            'name_font_size' => 'required|integer|min:10|max:100',
            'name_color' => 'required|string',
            'role_x' => 'required|numeric|min:0|max:210',
            'role_y' => 'required|numeric|min:0|max:297',
            'role_font_size' => 'required|integer|min:10|max:100',
            'role_color' => 'required|string',
            'event_name_x' => 'required|numeric|min:0|max:210',
            'event_name_y' => 'required|numeric|min:0|max:297',
            'event_name_font_size' => 'required|integer|min:10|max:100',
            'event_name_color' => 'required|string',
            'date_x' => 'required|numeric|min:0|max:210',
            'date_y' => 'required|numeric|min:0|max:297',
            'date_font_size' => 'required|integer|min:10|max:100',
            'date_color' => 'required|string',
        ]);

        $uploaded = false;

        // Upload participant template
        if ($request->hasFile('participant_template')) {
            $file = $request->file('participant_template');
            
            // Upload to Cloudinary
            $uploadResult = $this->cloudinaryService->uploadImage(
                $file,
                'certificate_templates/' . $event->id
            );

            // Delete old template
            $oldTemplate = CertificateTemplate::where('event_id', $event->id)
                ->where('template_type', 'participant')
                ->first();

            if ($oldTemplate && $oldTemplate->cloudinary_public_id) {
                $this->cloudinaryService->deleteFile($oldTemplate->cloudinary_public_id);
                $oldTemplate->delete();
            }

            // Create new template
            CertificateTemplate::create([
                'event_id' => $event->id,
                'template_type' => 'participant',
                'template_url' => $uploadResult['secure_url'],
                'cloudinary_public_id' => $uploadResult['public_id'],
                'name_x' => $request->name_x,
                'name_y' => $request->name_y,
                'name_font_size' => $request->name_font_size,
                'name_color' => $request->name_color,
                'role_x' => $request->role_x,
                'role_y' => $request->role_y,
                'role_font_size' => $request->role_font_size,
                'role_color' => $request->role_color,
                'event_name_x' => $request->event_name_x,
                'event_name_y' => $request->event_name_y,
                'event_name_font_size' => $request->event_name_font_size,
                'event_name_color' => $request->event_name_color,
                'date_x' => $request->date_x,
                'date_y' => $request->date_y,
                'date_font_size' => $request->date_font_size,
                'date_color' => $request->date_color,
            ]);
            $uploaded = true;
        }

        // Upload reviewer template
        if ($request->hasFile('reviewer_template')) {
            $file = $request->file('reviewer_template');
            
            // Upload to Cloudinary
            $uploadResult = $this->cloudinaryService->uploadImage(
                $file,
                'certificate_templates/' . $event->id
            );

            // Delete old template
            $oldTemplate = CertificateTemplate::where('event_id', $event->id)
                ->where('template_type', 'reviewer')
                ->first();

            if ($oldTemplate && $oldTemplate->cloudinary_public_id) {
                $this->cloudinaryService->deleteFile($oldTemplate->cloudinary_public_id);
                $oldTemplate->delete();
            }

            // Create new template
            CertificateTemplate::create([
                'event_id' => $event->id,
                'template_type' => 'reviewer',
                'template_url' => $uploadResult['secure_url'],
                'cloudinary_public_id' => $uploadResult['public_id'],
                'name_x' => $request->name_x,
                'name_y' => $request->name_y,
                'name_font_size' => $request->name_font_size,
                'name_color' => $request->name_color,
                'role_x' => $request->role_x,
                'role_y' => $request->role_y,
                'role_font_size' => $request->role_font_size,
                'role_color' => $request->role_color,
                'event_name_x' => $request->event_name_x,
                'event_name_y' => $request->event_name_y,
                'event_name_font_size' => $request->event_name_font_size,
                'event_name_color' => $request->event_name_color,
                'date_x' => $request->date_x,
                'date_y' => $request->date_y,
                'date_font_size' => $request->date_font_size,
                'date_color' => $request->date_color,
            ]);
            $uploaded = true;
        }

        // Check if templates already exist even if no new upload
        $existingParticipantTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'participant')
            ->exists();
        
        $existingReviewerTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'reviewer')
            ->exists();

        if (!$uploaded && !$existingParticipantTemplate && !$existingReviewerTemplate) {
            return redirect()->back()->with('error', 'Please upload at least one certificate template.');
        }

        // Auto-generate certificates after upload or regenerate with existing templates
        try {
            $result = $this->generateCertificatesForEvent($event);
            $successMessage = $uploaded 
                ? "Certificate template(s) uploaded successfully! {$result['message']}"
                : "Certificates regenerated! {$result['message']}";
            
            return redirect()->route('organizer.template-certificates.attendees', $event)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error generating certificates: ' . $e->getMessage());
            if ($uploaded) {
                return redirect()->route('organizer.template-certificates.attendees', $event)
                    ->with('success', 'Certificate template(s) uploaded successfully! Please generate certificates manually.');
            } else {
                return redirect()->back()->with('error', 'Error generating certificates: ' . $e->getMessage());
            }
        }
    }

    public function generateAll(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $result = $this->generateCertificatesForEvent($event);
        return redirect()->back()->with('success', $result['message']);
    }

    private function generateCertificatesForEvent(Event $event)
    {
        // Get all checked-in registrations
        $checkedInRegistrations = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('checked_in_at')
            ->with(['user', 'juryMappingsAsReviewer'])
            ->get();

        $participantTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'participant')
            ->first();

        $reviewerTemplate = CertificateTemplate::where('event_id', $event->id)
            ->where('template_type', 'reviewer')
            ->first();

        if (!$participantTemplate && !$reviewerTemplate) {
            throw new \Exception('Please upload certificate templates first.');
        }

        $generated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($checkedInRegistrations as $registration) {
            $isApprovedPresenter = $registration->presentation_status === 'selected';
            $isReviewer = $registration->juryMappingsAsReviewer->count() > 0;

            // Generate participant certificate
            if ($isApprovedPresenter && $participantTemplate) {
                $role = $isReviewer ? 'Participant & Reviewer' : 'Participant';
                try {
                    $result = SimpleCertificateGenerator::generate($event, $registration, $participantTemplate, 'participant', $role);
                    if ($result) $generated++; else $skipped++;
                } catch (\Exception $e) {
                    $errors[] = "Failed for {$registration->user->name}: {$e->getMessage()}";
                    \Log::error('Certificate generation error: ' . $e->getMessage());
                }
            }

            // Generate reviewer certificate (if they're reviewer only)
            if ($isReviewer && !$isApprovedPresenter && $reviewerTemplate) {
                try {
                    $result = SimpleCertificateGenerator::generate($event, $registration, $reviewerTemplate, 'reviewer', 'Reviewer');
                    if ($result) $generated++; else $skipped++;
                } catch (\Exception $e) {
                    $errors[] = "Failed for {$registration->user->name}: {$e->getMessage()}";
                    \Log::error('Certificate generation error: ' . $e->getMessage());
                }
            }
        }

        $message = "Generated {$generated} new certificate(s)";
        if ($skipped > 0) {
            $message .= " ({$skipped} already existed)";
        }
        if (count($errors) > 0) {
            $message .= ". Errors: " . count($errors);
        }

        return ['generated' => $generated, 'skipped' => $skipped, 'errors' => $errors, 'message' => $message];
    }

    private function generateCertificate(Event $event, EventRegistration $registration, CertificateTemplate $template, $type, $role)
    {
        // Check if already generated
        $existing = GeneratedCertificate::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->where('certificate_type', $type)
            ->first();

        if ($existing) {
            return false; // Skip if already generated
        }

        try {
            // Download template from Cloudinary
            \Log::info("Generating certificate for user {$registration->user_id}, type: {$type}");
            
            $templateImage = file_get_contents($template->template_url);
            if (!$templateImage) {
                throw new \Exception('Failed to download template from Cloudinary');
            }
            
            // Detect image type from content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($templateImage);
            \Log::info("Template mime type: {$mimeType}");
            
            // Save with appropriate extension
            $ext = ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') ? '.jpg' : '.png';
            $tempTemplatePath = storage_path('app/temp/template_' . uniqid() . $ext);
            
            // Create temp directory if doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            
            file_put_contents($tempTemplatePath, $templateImage);

            // Load image based on type
            if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
                $image = imagecreatefromjpeg($tempTemplatePath);
            } else if ($mimeType === 'image/png') {
                $image = imagecreatefrompng($tempTemplatePath);
            } else {
                throw new \Exception("Unsupported image type: {$mimeType}");
            }
            
            if (!$image) {
                throw new \Exception('Failed to load template image');
            }

            // Prepare text data
            $name = $registration->user->name;
            $eventName = $event->title;
            $eventDate = $event->start_date->format('F d, Y');

            // Convert hex color to RGB
            $nameColor = $this->hexToRgb($template->name_color);
            $roleColor = $this->hexToRgb($template->role_color);
            $eventNameColor = $this->hexToRgb($template->event_name_color);
            $dateColor = $this->hexToRgb($template->date_color);

            // Font path - using PHP's default font for now
            // You can add custom TTF fonts later
            $fontPath = public_path('fonts/arial.ttf');
            $useBuiltinFont = !file_exists($fontPath);

            if (!$useBuiltinFont) {
                // Convert mm to pixels
                $nameX = round($template->name_x * self::MM_TO_PX);
                $nameY = round($template->name_y * self::MM_TO_PX);
                $roleX = round($template->role_x * self::MM_TO_PX);
                $roleY = round($template->role_y * self::MM_TO_PX);
                $eventNameX = round($template->event_name_x * self::MM_TO_PX);
                $eventNameY = round($template->event_name_y * self::MM_TO_PX);
                $dateX = round($template->date_x * self::MM_TO_PX);
                $dateY = round($template->date_y * self::MM_TO_PX);

                // Add text using TrueType font
                // Name
                imagettftext($image, $template->name_font_size, 0, $nameX, $nameY, 
                    imagecolorallocate($image, $nameColor['r'], $nameColor['g'], $nameColor['b']), 
                    $fontPath, $name);

                // Role
                imagettftext($image, $template->role_font_size, 0, $roleX, $roleY, 
                    imagecolorallocate($image, $roleColor['r'], $roleColor['g'], $roleColor['b']), 
                    $fontPath, $role);

                // Event Name
                imagettftext($image, $template->event_name_font_size, 0, $eventNameX, $eventNameY, 
                    imagecolorallocate($image, $eventNameColor['r'], $eventNameColor['g'], $eventNameColor['b']), 
                    $fontPath, $eventName);

                // Date
                imagettftext($image, $template->date_font_size, 0, $dateX, $dateY, 
                    imagecolorallocate($image, $dateColor['r'], $dateColor['g'], $dateColor['b']), 
                    $fontPath, $eventDate);
            } else {
                // Convert mm to pixels
                $nameX = round($template->name_x * self::MM_TO_PX);
                $nameY = round($template->name_y * self::MM_TO_PX);
                $roleX = round($template->role_x * self::MM_TO_PX);
                $roleY = round($template->role_y * self::MM_TO_PX);
                $eventNameX = round($template->event_name_x * self::MM_TO_PX);
                $eventNameY = round($template->event_name_y * self::MM_TO_PX);
                $dateX = round($template->date_x * self::MM_TO_PX);
                $dateY = round($template->date_y * self::MM_TO_PX);

                // Fallback: use imagestring for basic text
                imagestring($image, 5, $nameX, $nameY, 
                    $name, imagecolorallocate($image, $nameColor['r'], $nameColor['g'], $nameColor['b']));
                imagestring($image, 4, $roleX, $roleY, 
                    $role, imagecolorallocate($image, $roleColor['r'], $roleColor['g'], $roleColor['b']));
                imagestring($image, 4, $eventNameX, $eventNameY, 
                    $eventName, imagecolorallocate($image, $eventNameColor['r'], $eventNameColor['g'], $eventNameColor['b']));
                imagestring($image, 3, $dateX, $dateY, 
                    $eventDate, imagecolorallocate($image, $dateColor['r'], $dateColor['g'], $dateColor['b']));
            }

            // Save to temp file
            $tempCertPath = storage_path('app/temp/cert_' . uniqid() . '.png');
            imagepng($image, $tempCertPath);
            imagedestroy($image);

            // Upload to Cloudinary using Cloudinary facade directly
            \Log::info("Uploading certificate to Cloudinary for user {$registration->user_id}");
            $uploadedFile = Cloudinary::upload($tempCertPath, [
                'folder' => 'certificates/' . $event->id . '/' . $type,
                'resource_type' => 'image',
            ]);

            $certUrl = $uploadedFile->getSecurePath();
            $certPublicId = $uploadedFile->getPublicId();

            \Log::info("Certificate uploaded successfully: {$certUrl}");

            // Save to database
            $certificate = GeneratedCertificate::create([
                'event_id' => $event->id,
                'user_id' => $registration->user_id,
                'registration_id' => $registration->id,
                'template_id' => $template->id,
                'certificate_url' => $certUrl,
                'cloudinary_public_id' => $certPublicId,
                'certificate_type' => $type,
                'participant_name' => $name,
                'participant_role' => $role,
                'event_name' => $eventName,
                'event_date' => $eventDate,
                'generated_at' => now(),
            ]);

            \Log::info("Certificate record created with ID: {$certificate->id}");

            // Cleanup
            unlink($tempTemplatePath);
            unlink($tempCertPath);

            return true;
        } catch (\Exception $e) {
            \Log::error("Certificate generation error for user {$registration->user_id}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e; // Re-throw to be caught by calling method
        }
    }

    private function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    public function download(GeneratedCertificate $certificate)
    {
        $user = Auth::guard('organizer')->user() ?? Auth::user();
        
        // Allow organizer or the certificate owner to download
        if (Auth::guard('organizer')->check()) {
            if ($certificate->event->organizer_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($certificate->user_id !== $user->id) {
                abort(403);
            }
        }

        $certificate->update(['downloaded_at' => now()]);

        return redirect($certificate->certificate_url);
    }
}
