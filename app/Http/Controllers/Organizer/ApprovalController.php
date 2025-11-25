<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Mail\JuryApproved;
use App\Mail\JuryRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    /**
     * Display event registrations pending approval
     */
    public function index(Request $request)
    {
        $events = Event::with(['registrations' => function($query) {
            $query->with('user')->pendingApproval();
        }])->where('organizer_id', Auth::id())->get();

        $filter = $request->get('filter', 'all');
        $role = $request->get('role', 'all');

        if ($filter === 'pending') {
            $events = $events->filter(function($event) {
                return $event->registrations->where('approval_status', 'pending')->count() > 0;
            });
        }

        return view('organizer.approvals.index', compact('events', 'filter', 'role'));
    }

    /**
     * Display registrations for a specific event
     */
    public function eventRegistrations(Event $event, Request $request)
    {
        $this->authorize('manage', $event);

        $query = $event->registrations()->with('user');
        
        $status = $request->get('status', 'all');
        $role = $request->get('role', 'all');

        if ($status !== 'all') {
            $query->where('approval_status', $status);
        }

        if ($role !== 'all') {
            if ($role === 'participant') {
                $query->participants();
            } elseif ($role === 'jury') {
                $query->jury();
            }
        }

        $registrations = $query->orderBy('registered_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => $event->registrations()->count(),
            'pending' => $event->registrations()->pendingApproval()->count(),
            'approved' => $event->registrations()->approved()->count(),
            'rejected' => $event->registrations()->where('approval_status', 'rejected')->count(),
            'participants' => $event->registrations()->participants()->count(),
            'jury' => $event->registrations()->jury()->count(),
        ];

        return view('organizer.approvals.event-registrations', compact('event', 'registrations', 'stats', 'status', 'role'));
    }

    /**
     * Approve a registration
     */
    public function approve(EventRegistration $registration, Request $request)
    {
        $this->authorize('manage', $registration->event);

        try {
            $registration->approve(Auth::id());

            // Send email notification for jury approval
            if (in_array($registration->role, ['jury', 'both'])) {
                Mail::to($registration->user->email)->send(new JuryApproved($registration));
            }

            Log::info("Registration approved", [
                'registration_id' => $registration->id,
                'event' => $registration->event->title,
                'user' => $registration->user->name,
                'role' => $registration->role,
                'approved_by' => Auth::user()->name
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration approved successfully and email sent to user',
                    'registration' => $registration->fresh()
                ]);
            }

            return back()->with('success', 'Registration approved successfully and email sent to user');

        } catch (\Exception $e) {
            Log::error("Failed to approve registration", [
                'registration_id' => $registration->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve registration'
                ], 500);
            }

            return back()->with('error', 'Failed to approve registration');
        }
    }

    /**
     * Reject a registration
     */
    public function reject(EventRegistration $registration, Request $request)
    {
        $this->authorize('manage', $registration->event);

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $registration->reject(Auth::id());
            
            // Update rejection reason if provided
            if ($request->filled('reason')) {
                $registration->update(['rejected_reason' => $request->reason]);
            }

            // Send email notification for jury rejection
            if (in_array($registration->role, ['jury', 'both'])) {
                Mail::to($registration->user->email)->send(
                    new JuryRejected($registration, $request->reason)
                );
            }

            Log::info("Registration rejected", [
                'registration_id' => $registration->id,
                'event' => $registration->event->title,
                'user' => $registration->user->name,
                'role' => $registration->role,
                'reason' => $request->reason,
                'rejected_by' => Auth::user()->name
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration rejected successfully and email sent to user',
                    'registration' => $registration->fresh()
                ]);
            }

            return back()->with('success', 'Registration rejected successfully');

        } catch (\Exception $e) {
            Log::error("Failed to reject registration", [
                'registration_id' => $registration->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject registration'
                ], 500);
            }

            return back()->with('error', 'Failed to reject registration');
        }
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'registrations' => 'required|array',
            'registrations.*' => 'exists:event_registrations,id'
        ]);

        $approved = 0;
        $failed = 0;

        foreach ($request->registrations as $registrationId) {
            try {
                $registration = EventRegistration::findOrFail($registrationId);
                $this->authorize('manage', $registration->event);
                
                $registration->approve(Auth::id());
                $approved++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Bulk approve failed for registration {$registrationId}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Approved {$approved} registrations";
        if ($failed > 0) {
            $message .= ", {$failed} failed";
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'approved' => $approved,
                'failed' => $failed
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk reject registrations
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'registrations' => 'required|array',
            'registrations.*' => 'exists:event_registrations,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $rejected = 0;
        $failed = 0;

        foreach ($request->registrations as $registrationId) {
            try {
                $registration = EventRegistration::findOrFail($registrationId);
                $this->authorize('manage', $registration->event);
                
                $registration->reject(Auth::id());
                
                if ($request->filled('reason')) {
                    $registration->update(['rejected_reason' => $request->reason]);
                }
                
                $rejected++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Bulk reject failed for registration {$registrationId}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Rejected {$rejected} registrations";
        if ($failed > 0) {
            $message .= ", {$failed} failed";
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'rejected' => $rejected,
                'failed' => $failed
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get registration details for modal view
     */
    public function show(EventRegistration $registration, Request $request)
    {
        $this->authorize('manage', $registration->event);

        if ($request->ajax()) {
            return response()->json([
                'registration' => $registration->load(['user', 'event']),
                'html' => view('organizer.approvals.partials.registration-details', compact('registration'))->render()
            ]);
        }

        return view('organizer.approvals.show', compact('registration'));
    }
}
