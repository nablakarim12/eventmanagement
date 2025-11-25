<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RegistrationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Registration Approval Controller
 * Handles approval workflow for jury applications
 */
class RegistrationApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show all registrations for an event (for approval)
     */
    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all registrations grouped by role and status
        $registrations = [
            'participants' => $event->registrations()
                                   ->where('role_type', 'participant')
                                   ->with(['user', 'files'])
                                   ->get(),
            'jury_pending' => $event->registrations()
                                   ->where('role_type', 'jury')
                                   ->where('approval_status', 'pending')
                                   ->with(['user', 'files'])
                                   ->get(),
            'jury_approved' => $event->registrations()
                                    ->where('role_type', 'jury')
                                    ->where('approval_status', 'approved')
                                    ->with(['user', 'files'])
                                    ->get(),
            'jury_rejected' => $event->registrations()
                                    ->where('role_type', 'jury')
                                    ->where('approval_status', 'rejected')
                                    ->with(['user', 'files'])
                                    ->get(),
        ];

        $stats = [
            'total_registrations' => $event->registrations()->count(),
            'participants_count' => $registrations['participants']->count(),
            'jury_pending_count' => $registrations['jury_pending']->count(),
            'jury_approved_count' => $registrations['jury_approved']->count(),
            'jury_rejected_count' => $registrations['jury_rejected']->count(),
        ];

        return view('organizer.registrations.approval', compact('event', 'registrations', 'stats'));
    }

    /**
     * Show detailed view of a single registration for approval
     */
    public function show(Event $event, EventRegistration $registration)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $registration->event_id !== $event->id) {
            abort(403);
        }

        $registration->load(['user', 'files', 'formData']);

        return view('organizer.registrations.show', compact('event', 'registration'));
    }

    /**
     * Approve a jury application
     */
    public function approve(Event $event, EventRegistration $registration, Request $request)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $registration->event_id !== $event->id) {
            abort(403);
        }

        // Only jury applications can be approved
        if (!in_array($registration->role_type, ['jury', 'both'])) {
            return back()->with('error', 'Only jury applications require approval.');
        }

        // Update approval status
        $registration->update([
            'approval_status' => 'approved',
            'approved_by' => $organizer->id,
            'approved_at' => now(),
            'rejection_reason' => null, // Clear any previous rejection reason
        ]);

        // Log the approval action (optional)
        activity()
            ->performedOn($registration)
            ->causedBy($organizer)
            ->withProperties(['notes' => $request->approval_notes])
            ->log('Jury application approved');

        return back()->with('success', 'Jury application approved successfully!');
    }

    /**
     * Reject a jury application
     */
    public function reject(Event $event, EventRegistration $registration, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $registration->event_id !== $event->id) {
            abort(403);
        }

        // Only jury applications can be rejected
        if (!in_array($registration->role_type, ['jury', 'both'])) {
            return back()->with('error', 'Only jury applications require approval.');
        }

        // Update rejection status
        $registration->update([
            'approval_status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Log the rejection action (optional)
        activity()
            ->performedOn($registration)
            ->causedBy($organizer)
            ->withProperties(['reason' => $request->rejection_reason])
            ->log('Jury application rejected');

        return back()->with('success', 'Jury application rejected.');
    }

    /**
     * Bulk approve multiple jury applications
     */
    public function bulkApprove(Event $event, Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
            'bulk_notes' => 'nullable|string|max:1000'
        ]);

        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $approvedCount = 0;

        foreach ($request->registration_ids as $registrationId) {
            $registration = EventRegistration::where('id', $registrationId)
                                            ->where('event_id', $event->id)
                                            ->whereIn('role_type', ['jury', 'both'])
                                            ->where('approval_status', 'pending')
                                            ->first();

            if ($registration) {
                $registration->update([
                    'approval_status' => 'approved',
                    'approved_by' => $organizer->id,
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);
                $approvedCount++;
            }
        }

        return back()->with('success', "Successfully approved {$approvedCount} jury applications!");
    }

    /**
     * Get users eligible for QR code generation
     */
    public function qrEligible(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $eligibleUsers = $event->registrations()
            ->where(function($query) {
                $query->where(function($q) {
                    // Participants who submitted materials
                    $q->where('role_type', 'participant')
                      ->where('materials_submitted', true);
                })->orWhere(function($q) {
                    // Approved jury members
                    $q->where('role_type', 'jury')
                      ->where('approval_status', 'approved');
                })->orWhere(function($q) {
                    // Both role: must have materials AND be approved
                    $q->where('role_type', 'both')
                      ->where('materials_submitted', true)
                      ->where('approval_status', 'approved');
                });
            })
            ->with(['user'])
            ->get();

        return response()->json([
            'eligible_count' => $eligibleUsers->count(),
            'eligible_users' => $eligibleUsers->map(function($registration) {
                return [
                    'user_id' => $registration->user_id,
                    'user_name' => $registration->user->name,
                    'role_type' => $registration->role_type,
                    'status' => $registration->approval_status,
                    'eligible_for_qr' => true
                ];
            })
        ]);
    }
}