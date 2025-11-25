<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's registration statistics
        $totalRegistrations = $user->eventRegistrations()->count();
        $upcomingEvents = $user->eventRegistrations()
            ->with('event')
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', Carbon::now());
            })
            ->where('status', '!=', 'cancelled')
            ->count();
            
        $pastEvents = $user->eventRegistrations()
            ->with('event')
            ->whereHas('event', function ($query) {
                $query->where('start_date', '<', Carbon::now());
            })
            ->where('status', '!=', 'cancelled')
            ->count();

        // Get recent registrations
        $recentRegistrations = $user->eventRegistrations()
            ->with(['event', 'event.category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming events user is registered for
        $upcomingRegisteredEvents = $user->eventRegistrations()
            ->with(['event', 'event.category', 'event.organizer'])
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', Carbon::now());
            })
            ->where('status', '!=', 'cancelled')
            ->orderBy('event.start_date', 'asc')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'totalRegistrations',
            'upcomingEvents', 
            'pastEvents',
            'recentRegistrations',
            'upcomingRegisteredEvents'
        ));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        return view('dashboard.profile');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        Auth::user()->update($validated);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show attendance form for an event (backup if QR not working)
     */
    public function showAttendanceForm(Event $event)
    {
        $user = Auth::user();
        
        // Check if user has a registration for this event
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$registration) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not registered for this event or your registration is not approved yet.');
        }

        // Check if already checked in
        if ($registration->checked_in_at) {
            return redirect()->route('dashboard.registrations.show', $registration)
                ->with('info', 'You have already checked in at ' . $registration->checked_in_at->format('M d, Y h:i A'));
        }

        // Check if event has started (allow check-in from 1 hour before)
        $allowCheckInFrom = Carbon::parse($event->start_date)->subHour();
        if (Carbon::now()->lt($allowCheckInFrom)) {
            return redirect()->route('dashboard.registrations.show', $registration)
                ->with('error', 'Check-in is not yet available. You can check in starting 1 hour before the event.');
        }

        return view('dashboard.attendance.form', compact('event', 'registration'));
    }

    /**
     * Submit manual attendance (backup if QR not working)
     */
    public function submitAttendance(Request $request, Event $event)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'registration_id' => 'required|exists:event_registrations,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'reason' => 'required|string|in:qr_not_working,forgot_qr,technical_issue,other',
            'additional_notes' => 'nullable|string|max:500',
        ]);

        // Verify registration belongs to user
        $registration = EventRegistration::where('id', $validated['registration_id'])
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Invalid registration.');
        }

        // Check if already checked in
        if ($registration->checked_in_at) {
            return redirect()->route('dashboard.registrations.show', $registration)
                ->with('info', 'You have already checked in.');
        }

        // Verify name and email match
        if (strtolower($validated['full_name']) !== strtolower($user->name) || 
            strtolower($validated['email']) !== strtolower($user->email)) {
            return back()->with('error', 'Name and email must match your account details.');
        }

        // Mark as checked in
        $registration->checked_in_at = now();
        $registration->save();

        // Log the manual check-in (optional - for tracking)
        // You could create a separate table for this if needed

        return redirect()->route('dashboard.registrations.show', $registration)
            ->with('success', 'Attendance submitted successfully! You are now checked in.');
    }
}
