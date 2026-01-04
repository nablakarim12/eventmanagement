<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PresentationCheckInController extends Controller
{
    /**
     * Display check-in page for conference event
     */
    public function index(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get all approved participants for presentation with attendance
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('presentation_status', 'selected')
            ->with([
                'user',
                'attendance' => function($query) {
                    $query->where('attendance_type', 'presentation');
                }
            ])
            ->orderBy('presentation_queue')
            ->get();

        // Add QR code URL for each participant
        foreach ($participants as $participant) {
            $participant->qr_code_url = $this->getOrGenerateQrCode($event, $participant);
        }

        // Get check-in statistics
        $stats = [
            'total_approved' => $participants->count(),
            'checked_in' => $participants->where('attendance')->count(),
            'pending' => $participants->whereNull('attendance')->count(),
        ];

        return view('organizer.presentation-checkin.index', compact('event', 'participants', 'stats'));
    }

    /**
     * Get or generate QR code for participant
     */
    private function getOrGenerateQrCode(Event $event, EventRegistration $registration)
    {
        // Check if QR code already exists
        $qrCodeRecord = DB::table('presentation_qr_codes')
            ->where('event_id', $event->id)
            ->where('registration_id', $registration->id)
            ->first();

        if ($qrCodeRecord) {
            return $qrCodeRecord->qr_image_url;
        }

        // Generate new QR code
        return $this->generateQrCode($event, $registration);
    }

    /**
     * Generate QR code and upload to Cloudinary
     */
    private function generateQrCode(Event $event, EventRegistration $registration)
    {
        // Create unique QR data
        $qrData = [
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'user_id' => $registration->user_id,
            'type' => 'presentation_checkin',
            'timestamp' => time(),
        ];

        $qrString = base64_encode(json_encode($qrData));

        // Generate QR code image using Endroid v6
        $qrCode = new QrCode($qrString);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save temporarily
        $tempPath = storage_path('app/temp/qr_' . time() . '.png');
        
        // Make sure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        file_put_contents($tempPath, $result->getString());

        try {
            // Upload to Cloudinary
            $uploadedFile = Cloudinary::upload($tempPath, [
                'folder' => 'presentation_qr_codes',
                'public_id' => "qr_{$event->id}_{$registration->id}_" . time(),
            ]);

            $qrImageUrl = $uploadedFile->getSecurePath();

            // Store QR code record in database
            DB::table('presentation_qr_codes')->insert([
                'event_id' => $event->id,
                'registration_id' => $registration->id,
                'user_id' => $registration->user_id,
                'qr_data' => $qrString,
                'qr_image_url' => $qrImageUrl,
                'cloudinary_public_id' => $uploadedFile->getPublicId(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Delete temp file
            @unlink($tempPath);

            return $qrImageUrl;

        } catch (\Exception $e) {
            @unlink($tempPath);
            throw $e;
        }
    }

    /**
     * Generate QR codes for all approved participants
     */
    public function generateAllQrCodes(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $participants = EventRegistration::where('event_id', $event->id)
            ->where('presentation_status', 'selected')
            ->get();

        $generated = 0;
        foreach ($participants as $participant) {
            try {
                $this->getOrGenerateQrCode($event, $participant);
                $generated++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return back()->with('success', "Generated {$generated} QR codes successfully!");
    }

    /**
     * Manual check-in
     */
    public function manualCheckIn(Request $request, Event $event, EventRegistration $registration)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Verify participant is approved for presentation
        if ($registration->presentation_status !== 'selected') {
            return back()->with('error', 'Participant is not approved for presentation.');
        }

        // Check if already checked in
        $existing = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->where('attendance_type', 'presentation')
            ->first();

        if ($existing) {
            return back()->with('info', 'Participant already checked in at ' . $existing->check_in_time->format('h:i A'));
        }

        // Create attendance record
        EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $registration->user_id,
            'registration_id' => $registration->id,
            'check_in_time' => now(),
            'check_in_method' => 'manual',
            'attendance_type' => 'presentation',
            'checked_in_by' => Auth::id(),
        ]);

        return back()->with('success', 'Participant checked in successfully!');
    }

    /**
     * QR code scanner page
     */
    public function scanner(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.presentation-checkin.scanner', compact('event'));
    }

    /**
     * Process QR code scan
     */
    public function processScan(Request $request, Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            // Decode QR data
            $qrData = json_decode(base64_decode($request->qr_data), true);

            // Verify event ID matches
            if ($qrData['event_id'] != $event->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is for a different event!'
                ]);
            }

            // Get registration
            $registration = EventRegistration::find($qrData['registration_id']);

            if (!$registration || $registration->presentation_status !== 'selected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Participant is not approved for presentation!'
                ]);
            }

            // Check if already checked in
            $existing = EventAttendance::where('event_id', $event->id)
                ->where('user_id', $registration->user_id)
                ->where('attendance_type', 'presentation')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in at ' . $existing->check_in_time->format('h:i A'),
                    'participant' => $registration->user->name,
                ]);
            }

            // Create attendance record
            $attendance = EventAttendance::create([
                'event_id' => $event->id,
                'user_id' => $registration->user_id,
                'registration_id' => $registration->id,
                'check_in_time' => now(),
                'check_in_method' => 'qr',
                'attendance_type' => 'presentation',
                'checked_in_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'participant' => [
                    'name' => $registration->user->name,
                    'email' => $registration->user->email,
                    'queue' => $registration->presentation_queue,
                    'time' => $attendance->check_in_time->format('h:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download participant's QR code
     */
    public function downloadQr(Event $event, EventRegistration $registration)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $qrCodeUrl = $this->getOrGenerateQrCode($event, $registration);

        return redirect($qrCodeUrl);
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn(Event $event, EventRegistration $registration)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        EventAttendance::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->where('attendance_type', 'presentation')
            ->delete();

        return back()->with('success', 'Check-in removed successfully!');
    }
}
