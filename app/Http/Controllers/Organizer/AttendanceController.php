<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\EventQrCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $events = Event::where('organizer_id', $organizer->id)
            ->withCount(['attendance', 'registrations'])
            ->latest()
            ->get();
        
        // Calculate statistics based on event_registrations.checked_in_at
        $totalRegistrations = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->where('approval_status', 'approved')->count();
        
        $totalCheckedIn = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->where('approval_status', 'approved')
          ->whereNotNull('checked_in_at')
          ->count();
        
        $currentlyAttending = $totalCheckedIn; // Currently attending = checked in (no checkout tracking in registrations)
        
        $stats = [
            'total_registered' => $totalRegistrations,
            'checked_in' => $totalCheckedIn,
            'currently_attending' => $currentlyAttending,
        ];
        
        // Fetch checked-in registrations instead of event_attendance records
        $query = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })
            ->whereNotNull('checked_in_at')
            ->with(['user', 'event']);
        
        // Apply filters
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereDate('checked_in_at', $date);
        }
        
        $attendanceRecords = $query->latest('checked_in_at')->paginate(10)->withQueryString();

        return view('organizer.attendance.index', compact('events', 'stats', 'attendanceRecords'));
    }

    public function event(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all registrations for this event (approved only)
        $allRegistrations = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->with(['user', 'juryAssignments'])
            ->get();

        // Statistics
        $totalRegistrations = $allRegistrations->count();
        $totalCheckedIn = $allRegistrations->where('checked_in_at', '!=', null)->count();
        
        // Jury statistics
        $juryRegistrations = $allRegistrations->whereIn('role', ['jury', 'both']);
        $totalJury = $juryRegistrations->count();
        $juryCheckedIn = $juryRegistrations->where('checked_in_at', '!=', null)->count();
        
        // Participant statistics
        $participantRegistrations = $allRegistrations->where('role', 'participant');
        $totalParticipants = $participantRegistrations->count();
        $participantsCheckedIn = $participantRegistrations->where('checked_in_at', '!=', null)->count();

        // Separate lists
        $allCheckedIn = $allRegistrations->where('checked_in_at', '!=', null)->sortByDesc('checked_in_at');
        $juryOnly = $juryRegistrations->where('checked_in_at', '!=', null)->sortByDesc('checked_in_at');
        $participantsOnly = $participantRegistrations->where('checked_in_at', '!=', null)->sortByDesc('checked_in_at');
        $notCheckedIn = $allRegistrations->where('checked_in_at', null);

        return view('organizer.attendance.event', compact(
            'event',
            'totalRegistrations',
            'totalCheckedIn',
            'totalJury',
            'juryCheckedIn',
            'totalParticipants',
            'participantsCheckedIn',
            'allCheckedIn',
            'juryOnly',
            'participantsOnly',
            'notCheckedIn'
        ));
    }

    /**
     * Manual check-in for QR-based registration (backup if QR not working)
     */
    public function manualRegistrationCheckIn(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'registration_id' => 'required|exists:event_registrations,id',
        ]);

        $registration = EventRegistration::where('id', $request->registration_id)
            ->where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Invalid registration or not approved.');
        }

        if ($registration->checked_in_at) {
            return back()->with('info', 'This person is already checked in at ' . $registration->checked_in_at->format('M d, Y h:i A'));
        }

        // Mark as checked in
        $registration->checked_in_at = now();
        $registration->save();

        return back()->with('success', $registration->user->name . ' has been checked in successfully!');
    }

    /**
     * Bulk manual check-in for multiple registrations
     */
    public function bulkRegistrationCheckIn(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'exists:event_registrations,id',
        ]);

        $checkedInCount = 0;
        $alreadyCheckedIn = 0;

        foreach ($request->registration_ids as $registrationId) {
            $registration = EventRegistration::where('id', $registrationId)
                ->where('event_id', $event->id)
                ->where('approval_status', 'approved')
                ->first();

            if ($registration && !$registration->checked_in_at) {
                $registration->checked_in_at = now();
                $registration->save();
                $checkedInCount++;
            } elseif ($registration && $registration->checked_in_at) {
                $alreadyCheckedIn++;
            }
        }

        $message = "Checked in {$checkedInCount} attendee(s).";
        if ($alreadyCheckedIn > 0) {
            $message .= " {$alreadyCheckedIn} were already checked in.";
        }

        return back()->with('success', $message);
    }

    public function manualCheckIn(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $request->user_id)
            ->where('status', 'confirmed')
            ->first();

        if (!$registration) {
            return back()->with('error', 'User is not registered for this event.');
        }

        $attendance = EventAttendance::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $request->user_id,
            ],
            [
                'registration_id' => $registration->id,
                'check_in_time' => now(),
                'check_in_method' => 'manual',
                'status' => 'present',
            ]
        );

        return back()->with('success', 'Participant checked in successfully.');
    }

    public function manualCheckOut(Request $request, Event $event, EventAttendance $attendance)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        if (!$attendance->check_in_time) {
            return back()->with('error', 'Cannot check out without checking in first.');
        }

        $attendance->checkOut('manual');

        return back()->with('success', 'Participant checked out successfully.');
    }

    public function bulkCheckIn(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $checkedIn = 0;
        $errors = [];

        foreach ($request->user_ids as $userId) {
            $registration = EventRegistration::where('event_id', $event->id)
                ->where('user_id', $userId)
                ->where('status', 'confirmed')
                ->first();

            if (!$registration) {
                $user = User::find($userId);
                $errors[] = "User {$user->name} is not registered for this event.";
                continue;
            }

            EventAttendance::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id' => $userId,
                ],
                [
                    'registration_id' => $registration->id,
                    'check_in_time' => now(),
                    'check_in_method' => 'bulk',
                    'status' => 'present',
                ]
            );

            $checkedIn++;
        }

        $message = "Successfully checked in {$checkedIn} participants.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', $errors);
        }

        return back()->with('success', $message);
    }

    public function qrScanner(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $qrCodes = EventQrCode::where('event_id', $event->id)
            ->where('is_active', true)
            ->get();

        return view('organizer.attendance.qr-scanner', compact('event', 'qrCodes'));
    }

    public function processQrScan(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData || !isset($qrData['qr_code'])) {
                return response()->json(['error' => 'Invalid QR code format'], 400);
            }

            $qrCode = EventQrCode::where('qr_code', $qrData['qr_code'])
                ->where('event_id', $event->id)
                ->first();

            if (!$qrCode || !$qrCode->is_valid) {
                return response()->json(['error' => 'Invalid or expired QR code'], 400);
            }

            // For participant QR codes, we need user identification
            // This would typically be handled by participant scanning their registration QR
            // For now, we'll implement event-based QR scanning
            
            $qrCode->incrementScanCount();

            return response()->json([
                'success' => true,
                'message' => 'QR code scanned successfully',
                'data' => [
                    'type' => $qrCode->type,
                    'scan_count' => $qrCode->scan_count,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process QR code'], 500);
        }
    }

    public function export(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $attendanceData = EventAttendance::where('event_id', $event->id)
            ->with(['user', 'registration'])
            ->get();

        $filename = 'attendance_' . $event->slug . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendanceData) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Name',
                'Email',
                'Check In Time',
                'Check Out Time',
                'Duration (Hours)',
                'Status',
                'Check In Method',
                'Check Out Method'
            ]);

            foreach ($attendanceData as $attendance) {
                fputcsv($file, [
                    $attendance->user->name,
                    $attendance->user->email,
                    $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : '',
                    $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : '',
                    $attendance->duration_hours ?: '',
                    $attendance->status,
                    $attendance->check_in_method,
                    $attendance->check_out_method
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function analytics(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $attendance = EventAttendance::where('event_id', $event->id)->get();
        $registrations = EventRegistration::where('event_id', $event->id)->where('status', 'confirmed')->count();

        $analytics = [
            'total_registered' => $registrations,
            'total_checked_in' => $attendance->whereNotNull('check_in_time')->count(),
            'total_checked_out' => $attendance->whereNotNull('check_out_time')->count(),
            'average_duration' => $attendance->whereNotNull('total_duration_minutes')->avg('total_duration_minutes'),
            'attendance_rate' => $registrations > 0 ? round(($attendance->whereNotNull('check_in_time')->count() / $registrations) * 100, 1) : 0,
            'hourly_checkins' => $this->getHourlyCheckIns($event->id),
            'daily_checkins' => $this->getDailyCheckIns($event->id),
        ];

        return view('organizer.attendance.analytics', compact('event', 'analytics'));
    }

    private function getHourlyCheckIns($eventId)
    {
        return EventAttendance::where('event_id', $eventId)
            ->whereNotNull('check_in_time')
            ->selectRaw('HOUR(check_in_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }

    private function getDailyCheckIns($eventId)
    {
        return EventAttendance::where('event_id', $eventId)
            ->whereNotNull('check_in_time')
            ->selectRaw('DATE(check_in_time) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }
    
    public function scanner()
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get organizer's events for the scanner dropdown
        $events = Event::where('organizer_id', $organizer->id)
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('organizer.attendance.scanner', compact('events'));
    }

    public function qrCheckIn(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $qrData = $request->input('qr_data');
        
        // Parse QR code data - assuming format: user_id:event_id
        $parts = explode(':', $qrData);
        
        if (count($parts) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format'
            ], 400);
        }
        
        $userId = $parts[0];
        $eventId = $parts[1];
        
        // Verify event belongs to organizer
        $event = Event::where('id', $eventId)
            ->where('organizer_id', $organizer->id)
            ->first();
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }
        
        // Verify user is registered for event
        $registration = EventRegistration::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->first();
        
        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not registered for this event'
            ], 404);
        }
        
        // Create or update attendance
        $attendance = EventAttendance::updateOrCreate(
            [
                'event_id' => $eventId,
                'user_id' => $userId,
            ],
            [
                'registration_id' => $registration->id,
                'check_in_time' => now(),
                'check_in_method' => 'qr_code',
                'status' => 'present',
            ]
        );
        
        $user = User::find($userId);
        
        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'event' => [
                'title' => $event->title,
            ]
        ]);
    }
}
