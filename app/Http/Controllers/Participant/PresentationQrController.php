<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\EventRegistration;

class PresentationQrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show participant's presentation QR code
     */
    public function show($eventId)
    {
        $user = Auth::user();
        
        // Get participant's registration
        $registration = EventRegistration::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('presentation_status', 'selected')
            ->with('event')
            ->firstOrFail();
        
        // Get QR code from database
        $qrCode = DB::table('presentation_qr_codes')
            ->where('event_id', $eventId)
            ->where('registration_id', $registration->id)
            ->where('user_id', $user->id)
            ->first();
        
        // Check attendance status
        $attendance = DB::table('event_attendance')
            ->where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('registration_id', $registration->id)
            ->where('attendance_type', 'presentation')
            ->first();
        
        return view('participant.presentation-qr', compact('registration', 'qrCode', 'attendance'));
    }

    /**
     * Download participant's QR code
     */
    public function download($eventId)
    {
        $user = Auth::user();
        
        $qrCode = DB::table('presentation_qr_codes')
            ->where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$qrCode || !$qrCode->qr_image_url) {
            return redirect()->back()->with('error', 'QR code not found.');
        }
        
        // Redirect to Cloudinary URL for download
        return redirect($qrCode->qr_image_url);
    }
}
