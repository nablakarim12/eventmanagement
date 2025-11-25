<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventQrCode;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicQrScanController extends Controller
{
    /**
     * Handle public QR code scanning for attendance
     * This endpoint will be called when users scan QR codes
     */
    public function scan($qrCode)
    {
        try {
            // Check if this is an API request
            if (request()->expectsJson()) {
                return $this->handleApiScan($qrCode);
            }
            
            // Return the QR scan view for web browsers
            return view('qr-scan', compact('qrCode'));
            
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process QR code'
                ], 500);
            }
            
            return view('qr-scan', ['qrCode' => $qrCode, 'error' => 'Failed to load QR code']);
        }
    }

    /**
     * Handle API scan requests
     */
    private function handleApiScan($qrCode)
    {
        // Find the QR code
        $eventQrCode = EventQrCode::where('qr_code', $qrCode)
            ->where('is_active', true)
            ->with(['event'])
            ->first();

        if (!$eventQrCode || !$eventQrCode->is_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code'
            ], 400);
        }

        $event = $eventQrCode->event;

        // Return event info for user to identify themselves
        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'date' => $event->event_date->format('F j, Y'),
                'time' => $event->event_date->format('g:i A'),
                'location' => $event->location,
                'qr_type' => $eventQrCode->type
            ],
            'qr_data' => [
                'qr_id' => $eventQrCode->id,
                'type' => $eventQrCode->type,
                'event_id' => $event->id
            ]
        ]);
    }

    /**
     * Process attendance check-in/check-out
     */
    public function processAttendance(Request $request)
    {
        $request->validate([
            'qr_id' => 'required|exists:event_qr_codes,id',
            'user_email' => 'required|email',
            'user_name' => 'required|string',
            'participant_type' => 'nullable|in:participant,jury'
        ]);

        try {
            DB::beginTransaction();

            $qrCode = EventQrCode::find($request->qr_id);
            $event = $qrCode->event;

            // Find or create user (simplified for demo)
            $user = User::where('email', $request->user_email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'password' => bcrypt('temporary_password'), // Should be handled by registration system
                ]);
            }

            // Check if user is registered for this event
            $registration = EventRegistration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$registration) {
                // Auto-register user for this event
                $registration = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'status' => 'confirmed',
                    'registered_at' => now(),
                    'registration_data' => [
                        'participant_type' => $request->participant_type ?? 'participant',
                        'registered_via' => 'qr_scan'
                    ]
                ]);
            }

            // Process attendance based on QR type
            $attendance = EventAttendance::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            $message = '';
            $action = '';

            if ($qrCode->type === 'check_in') {
                if (!$attendance) {
                    // New check-in
                    $attendance = EventAttendance::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'registration_id' => $registration->id,
                        'check_in_time' => now(),
                        'check_in_method' => 'qr_scan',
                        'check_in_location' => 'QR Scan',
                        'status' => 'present'
                    ]);
                    $message = 'Successfully checked in!';
                    $action = 'check_in';
                } else if ($attendance->check_in_time && !$attendance->check_out_time) {
                    $message = 'Already checked in at ' . $attendance->check_in_time->format('g:i A');
                    $action = 'already_checked_in';
                } else {
                    $message = 'Already processed for this event';
                    $action = 'already_processed';
                }
            } else if ($qrCode->type === 'check_out') {
                if ($attendance && $attendance->check_in_time && !$attendance->check_out_time) {
                    // Process check-out
                    $checkOutTime = now();
                    $duration = $attendance->check_in_time->diffInMinutes($checkOutTime);
                    
                    $attendance->update([
                        'check_out_time' => $checkOutTime,
                        'check_out_method' => 'qr_scan',
                        'check_out_location' => 'QR Scan',
                        'total_duration_minutes' => $duration,
                        'status' => 'completed'
                    ]);
                    
                    $message = 'Successfully checked out! Duration: ' . round($duration/60, 1) . ' hours';
                    $action = 'check_out';
                } else if (!$attendance || !$attendance->check_in_time) {
                    $message = 'Please check in first before checking out';
                    $action = 'not_checked_in';
                } else {
                    $message = 'Already checked out at ' . $attendance->check_out_time->format('g:i A');
                    $action = 'already_checked_out';
                }
            }

            // Increment QR scan count
            $qrCode->incrementScanCount();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $action,
                'attendance' => [
                    'check_in_time' => $attendance->check_in_time?->format('F j, Y g:i A'),
                    'check_out_time' => $attendance->check_out_time?->format('F j, Y g:i A'),
                    'duration_hours' => $attendance->total_duration_minutes ? round($attendance->total_duration_minutes/60, 1) : null,
                    'participant_type' => $registration->registration_data['participant_type'] ?? 'participant'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process attendance: ' . $e->getMessage()
            ], 500);
        }
    }
}