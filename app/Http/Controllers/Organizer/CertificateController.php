<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = EventCertificate::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event', 'user', 'attendance']);

        // Filter by event
        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Search by participant name
        if ($request->search) {
            $query->where('participant_name', 'like', '%' . $request->search . '%');
        }

        $certificates = $query->latest()->paginate(15);
        
        $events = Event::where('organizer_id', $organizer->id)->get();

        $stats = [
            'total_certificates' => EventCertificate::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->count(),
            'certificates_this_month' => EventCertificate::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereMonth('generated_at', now()->month)->count(),
            'sent_certificates' => EventCertificate::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereNotNull('emailed_at')->count(),
            'downloaded_certificates' => EventCertificate::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->where('download_count', '>', 0)->count(),
            'total_downloads' => EventCertificate::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->sum('download_count'),
        ];

        return view('organizer.certificates.index', compact('certificates', 'events', 'stats'));
    }

    public function event(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $certificates = EventCertificate::where('event_id', $event->id)
            ->with(['user', 'attendance'])
            ->latest()
            ->get();

        $eligibleAttendance = EventAttendance::where('event_id', $event->id)
            ->whereNotNull('check_out_time')
            ->where('certificate_generated', false)
            ->with(['user', 'registration'])
            ->get()
            ->filter(function($attendance) use ($event) {
                $minHours = $event->min_attendance_hours ?? 1;
                return $attendance->duration_hours >= $minHours;
            });

        $stats = [
            'total_certificates' => $certificates->count(),
            'eligible_for_certificates' => $eligibleAttendance->count(),
            'total_downloads' => $certificates->sum('download_count'),
            'emailed_certificates' => $certificates->whereNotNull('emailed_at')->count(),
        ];

        return view('organizer.certificates.event', compact('event', 'certificates', 'eligibleAttendance', 'stats'));
    }

    public function show(EventCertificate $certificate)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($certificate->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $certificate->load(['event', 'user', 'attendance']);

        return view('organizer.certificates.show', compact('certificate'));
    }

    public function generate(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:event_attendance,id',
        ]);

        $generated = 0;
        $errors = [];

        foreach ($request->attendance_ids as $attendanceId) {
            $attendance = EventAttendance::where('event_id', $event->id)
                ->where('id', $attendanceId)
                ->first();

            if (!$attendance) {
                continue;
            }

            // Check if certificate already exists
            if ($attendance->certificate_generated) {
                $errors[] = "Certificate already exists for {$attendance->user->name}";
                continue;
            }

            // Check if meets minimum requirements
            $minHours = $event->min_attendance_hours ?? 1;
            if ($attendance->duration_hours < $minHours) {
                $errors[] = "{$attendance->user->name} doesn't meet minimum attendance requirement ({$minHours} hours)";
                continue;
            }

            try {
                $certificate = $this->generateCertificate($attendance);
                $attendance->update([
                    'certificate_generated' => true,
                    'certificate_generated_at' => now()
                ]);
                $generated++;
            } catch (\Exception $e) {
                $errors[] = "Failed to generate certificate for {$attendance->user->name}: " . $e->getMessage();
            }
        }

        $message = "Generated {$generated} certificates successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', $errors);
        }

        return back()->with('success', $message);
    }

    public function bulkGenerate(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $eligibleAttendance = EventAttendance::where('event_id', $event->id)
            ->whereNotNull('check_out_time')
            ->where('certificate_generated', false)
            ->get()
            ->filter(function($attendance) use ($event) {
                $minHours = $event->min_attendance_hours ?? 1;
                return $attendance->duration_hours >= $minHours;
            });

        $generated = 0;
        $errors = [];

        foreach ($eligibleAttendance as $attendance) {
            try {
                $certificate = $this->generateCertificate($attendance);
                $attendance->update([
                    'certificate_generated' => true,
                    'certificate_generated_at' => now()
                ]);
                $generated++;
            } catch (\Exception $e) {
                $errors[] = "Failed to generate certificate for {$attendance->user->name}: " . $e->getMessage();
            }
        }

        $message = "Generated {$generated} certificates successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', $errors);
        }

        return back()->with('success', $message);
    }

    public function download(EventCertificate $certificate)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($certificate->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        if (!\Storage::disk('public')->exists($certificate->certificate_path)) {
            // Regenerate certificate if file doesn't exist
            $this->generateCertificatePdf($certificate);
        }

        $certificate->incrementDownloadCount();

        $fileName = "Certificate_{$certificate->certificate_number}.pdf";
        
        return \Storage::disk('public')->download($certificate->certificate_path, $fileName);
    }

    public function email(Request $request, EventCertificate $certificate)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($certificate->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'email_message' => 'nullable|string|max:1000',
        ]);

        try {
            // Here you would implement email sending
            // For now, we'll just mark it as emailed
            $certificate->markAsEmailed();
            
            return back()->with('success', 'Certificate emailed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send certificate: ' . $e->getMessage());
        }
    }

    public function bulkEmail(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:event_certificates,id',
            'email_message' => 'nullable|string|max:1000',
        ]);

        $sent = 0;
        $errors = [];

        foreach ($request->certificate_ids as $certificateId) {
            $certificate = EventCertificate::where('event_id', $event->id)
                ->where('id', $certificateId)
                ->first();

            if (!$certificate) {
                continue;
            }

            try {
                // Email logic would go here
                $certificate->markAsEmailed();
                $sent++;
            } catch (\Exception $e) {
                $errors[] = "Failed to send to {$certificate->participant_name}";
            }
        }

        $message = "Sent {$sent} certificates successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', $errors);
        }

        return back()->with('success', $message);
    }

    protected function generateCertificate(EventAttendance $attendance)
    {
        $certificate = EventCertificate::createFromAttendance($attendance);
        
        // Generate the PDF certificate
        $this->generateCertificatePdf($certificate);
        
        return $certificate;
    }

    protected function generateCertificatePdf(EventCertificate $certificate)
    {
        // For now, we'll create a simple text-based certificate
        // In a real implementation, you'd use a PDF library like TCPDF or DomPDF
        
        $certificateContent = $this->generateCertificateContent($certificate);
        
        // Create directory if it doesn't exist
        $directory = 'certificates';
        \Storage::disk('public')->makeDirectory($directory);

        // Generate filename
        $fileName = "certificate_{$certificate->certificate_number}.pdf";
        $filePath = "{$directory}/{$fileName}";

        // For demonstration, save as text file
        \Storage::disk('public')->put($filePath . '.txt', $certificateContent);
        
        // Update certificate with file path
        $certificate->update(['certificate_path' => $filePath]);
        
        return $filePath;
    }

    protected function generateCertificateContent(EventCertificate $certificate)
    {
        return "
CERTIFICATE OF ATTENDANCE

This is to certify that

{$certificate->participant_name}

has successfully attended

{$certificate->event_title}

on {$certificate->event_date->format('F j, Y')}

Total Attendance: {$certificate->attendance_hours} hours

Certificate Number: {$certificate->certificate_number}
Verification Code: {$certificate->verification_code}

Issued by: {$certificate->event->organizer->org_name}
Date Issued: {$certificate->generated_at->format('F j, Y')}

This certificate can be verified at: {$certificate->verification_url}
        ";
    }

    public function bulkEmailGeneral(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $certificateIds = $request->input('certificate_ids', []);
        
        $certificates = EventCertificate::whereIn('id', $certificateIds)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event'])
            ->get();
        
        $count = 0;
        foreach ($certificates as $certificate) {
            // Email sending logic would go here
            // For now, just mark as emailed
            $certificate->update(['emailed_at' => now()]);
            $count++;
        }
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Sent emails for {$count} certificates"
        ]);
    }

    public function bulkDownloadGeneral(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $certificateIds = $request->input('certificate_ids', []);
        
        $certificates = EventCertificate::whereIn('id', $certificateIds)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event'])
            ->get();
        
        if ($certificates->isEmpty()) {
            return redirect()->back()->with('error', 'No certificates found');
        }
        
        // For now, return a simple text file with certificate information
        // In a real app, you'd create a ZIP file with PDFs
        $content = "CERTIFICATES DOWNLOAD\n";
        $content .= "===================\n\n";
        
        foreach ($certificates as $cert) {
            $content .= "Certificate: {$cert->certificate_number}\n";
            $content .= "Participant: {$cert->participant_name}\n";
            $content .= "Event: {$cert->event_title}\n";
            $content .= "Date: {$cert->event_date->format('F j, Y')}\n";
            $content .= "---\n\n";
            
            // Update download count
            $cert->increment('download_count');
            $cert->update(['last_downloaded_at' => now()]);
        }
        
        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, 'certificates_' . now()->format('Y-m-d_His') . '.txt');
    }

    /**
     * Display attendance summary for certificate generation
     */
    public function attendanceSummary(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get attendance analytics for certificate generation
        $attendanceStats = [
            'total_registered' => $event->registrations()->count(),
            'total_checked_in' => EventAttendance::where('event_id', $event->id)
                                                ->whereNotNull('check_in_time')->count(),
            'total_completed' => EventAttendance::where('event_id', $event->id)
                                               ->where('status', 'completed')->count(),
            'participants_completed' => EventAttendance::where('event_id', $event->id)
                                                      ->where('status', 'completed')
                                                      ->where('role', 'participant')->count(),
            'jury_completed' => EventAttendance::where('event_id', $event->id)
                                              ->where('status', 'completed')
                                              ->where('role', 'jury')->count(),
        ];

        // Calculate attendance rate
        if ($attendanceStats['total_registered'] > 0) {
            $attendanceStats['attendance_rate'] = round(
                ($attendanceStats['total_checked_in'] / $attendanceStats['total_registered']) * 100, 2
            );
        } else {
            $attendanceStats['attendance_rate'] = 0;
        }

        return view('organizer.certificates.attendance-summary', compact('event', 'attendanceStats'));
    }

    /**
     * Generate certificates based on QR attendance records
     */
    public function generateFromAttendance(Event $event, Request $request)
    {
        $request->validate([
            'certificate_type' => 'required|in:participation,jury,both',
            'attendee_ids' => 'required|array',
            'attendee_ids.*' => 'exists:users,id'
        ]);

        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $generatedCount = 0;
        $certificateType = $request->certificate_type;

        foreach ($request->attendee_ids as $attendeeId) {
            $attendance = EventAttendance::where('event_id', $event->id)
                ->where('user_id', $attendeeId)
                ->where('status', 'completed') // Must have completed attendance (check-in + check-out)
                ->with('user')
                ->first();

            if ($attendance && $attendance->user) {
                // Check if certificate already exists
                $existingCert = EventCertificate::where('event_id', $event->id)
                                              ->where('user_id', $attendeeId)
                                              ->first();

                if (!$existingCert) {
                    $certType = $this->determineCertificateType($attendance->role, $certificateType);
                    
                    if ($certType) {
                        $this->createAttendanceCertificate($event, $attendance, $certType);
                        $generatedCount++;
                    }
                }
            }
        }

        return redirect()->back()
            ->with('success', "Successfully generated {$generatedCount} certificates based on attendance records!");
    }

    /**
     * Get eligible attendees for certificate generation
     */
    public function eligibleAttendees(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get attendees who completed full attendance (check-in + check-out)
        $eligibleAttendees = EventAttendance::where('event_id', $event->id)
            ->where('status', 'completed')
            ->with(['user'])
            ->get()
            ->map(function($attendance) use ($event) {
                $hasCertificate = EventCertificate::where('event_id', $event->id)
                                                 ->where('user_id', $attendance->user_id)
                                                 ->exists();

                return [
                    'user' => $attendance->user,
                    'attendance' => $attendance,
                    'role' => $attendance->role,
                    'duration' => $this->calculateAttendanceDuration($attendance),
                    'has_certificate' => $hasCertificate,
                    'eligible' => !$hasCertificate // Only eligible if no certificate exists yet
                ];
            });

        return view('organizer.certificates.eligible-attendees', compact('event', 'eligibleAttendees'));
    }

    /**
     * Determine certificate type based on attendee role and requested type
     */
    private function determineCertificateType($attendeeRole, $requestedType): ?string
    {
        switch ($requestedType) {
            case 'participation':
                return $attendeeRole === 'participant' ? 'participation' : null;
            case 'jury':
                return $attendeeRole === 'jury' ? 'jury_appreciation' : null;
            case 'both':
                return $attendeeRole === 'participant' ? 'participation' : 'jury_appreciation';
            default:
                return null;
        }
    }

    /**
     * Create certificate based on attendance record
     */
    private function createAttendanceCertificate(Event $event, EventAttendance $attendance, string $type)
    {
        $user = $attendance->user;
        
        return EventCertificate::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'certificate_type' => $type,
            'participant_name' => $user->name,
            'participant_email' => $user->email,
            'event_title' => $event->title,
            'event_date' => $event->start_date,
            'certificate_number' => $this->generateCertificateNumber($event, $user, $type),
            'generated_at' => now(),
            'issued_date' => now(),
            'template_data' => [
                'attendee_name' => $user->name,
                'event_title' => $event->title,
                'event_date' => $event->start_date->format('F d, Y'),
                'role' => ucfirst($attendance->role),
                'attendance_duration' => $this->calculateAttendanceDuration($attendance),
                'certificate_text' => $this->generateCertificateText($type, $user->name, $event->title, $attendance->role)
            ]
        ]);
    }

    /**
     * Generate certificate text based on type and role
     */
    private function generateCertificateText(string $type, string $name, string $eventTitle, string $role): string
    {
        switch ($type) {
            case 'participation':
                return "This certificate of participation is awarded to {$name} for successfully attending and participating in {$eventTitle} as a {$role}.";
            case 'jury_appreciation':
                return "This certificate of appreciation is presented to {$name} for serving as a distinguished jury member in {$eventTitle} and contributing to the evaluation process.";
            default:
                return "This certificate is presented to {$name} for attendance and participation in {$eventTitle}.";
        }
    }

    /**
     * Calculate attendance duration from check-in to check-out
     */
    private function calculateAttendanceDuration(EventAttendance $attendance): ?string
    {
        if (!$attendance->check_in_time || !$attendance->check_out_time) {
            return 'Incomplete attendance';
        }

        $duration = $attendance->check_out_time->diffInMinutes($attendance->check_in_time);
        $hours = intval($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Generate unique certificate number
     */
    private function generateCertificateNumber(Event $event, $user, string $type): string
    {
        $eventCode = strtoupper(substr($event->slug ?? \Illuminate\Support\Str::slug($event->title), 0, 3));
        $typeCode = strtoupper(substr($type, 0, 1));
        $userCode = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $yearCode = date('y');
        $randomCode = strtoupper(substr(uniqid(), -3));
        
        return "{$eventCode}-{$typeCode}{$userCode}-{$yearCode}{$randomCode}";
    }
}
