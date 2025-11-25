<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QrCheckInController extends Controller
{
    /**
     * Show the QR check-in page when user scans their QR code
     */
    public function scan($qrCode)
    {
        $registration = EventRegistration::where('qr_code', $qrCode)
            ->with(['event', 'user'])
            ->first();

        if (!$registration) {
            return view('qr.invalid', [
                'message' => 'Invalid QR code. Please ensure you are scanning a valid registration QR code.'
            ]);
        }

        // Check if registration is approved
        if (!$registration->approved_at) {
            return view('qr.not-approved', [
                'registration' => $registration,
                'message' => 'Your registration has not been approved yet.'
            ]);
        }

        // Check if event has started or is today
        $eventDate = Carbon::parse($registration->event->event_date);
        $now = Carbon::now();
        
        if ($eventDate->isFuture() && !$eventDate->isToday()) {
            return view('qr.too-early', [
                'registration' => $registration,
                'eventDate' => $eventDate,
                'message' => 'Check-in is not available yet. Event starts on ' . $eventDate->format('F j, Y')
            ]);
        }

        // Show check-in confirmation page
        return view('qr.check-in', [
            'registration' => $registration,
            'alreadyCheckedIn' => !is_null($registration->checked_in_at)
        ]);
    }

    /**
     * Process the check-in
     */
    public function checkIn(Request $request, $qrCode)
    {
        $registration = EventRegistration::where('qr_code', $qrCode)->first();

        if (!$registration) {
            return response()->json(['error' => 'Invalid QR code'], 404);
        }

        if (!$registration->approved_at) {
            return response()->json(['error' => 'Registration not approved'], 403);
        }

        // Mark as checked in
        if (!$registration->checked_in_at) {
            $registration->checked_in_at = Carbon::now();
            $registration->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully checked in!',
            'registration' => [
                'name' => $registration->user->name,
                'role' => $registration->role,
                'event' => $registration->event->event_name,
                'checked_in_at' => $registration->checked_in_at->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
