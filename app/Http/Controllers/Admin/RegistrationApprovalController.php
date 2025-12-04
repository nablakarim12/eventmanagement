<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Http\Request;

class RegistrationApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display all pending registrations
     */
    public function index(Request $request)
    {
        $query = EventRegistration::with(['event', 'user'])
            ->where('approval_status', 'pending');

        // Filter by event if specified
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by role if specified
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $registrations = $query->latest()->paginate(20);
        
        // Get all events for filter dropdown
        $events = Event::orderBy('start_date', 'desc')->get();

        return view('admin.approvals.index', compact('registrations', 'events'));
    }

    /**
     * Show details of a specific registration
     */
    public function show(EventRegistration $registration)
    {
        $registration->load(['event', 'user']);
        return view('admin.approvals.show', compact('registration'));
    }

    /**
     * Approve a registration
     */
    public function approve(EventRegistration $registration)
    {
        $registration->update([
            'approval_status' => 'approved',
            'approved_by' => auth('admin')->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Registration approved successfully.');
    }

    /**
     * Reject a registration
     */
    public function reject(Request $request, EventRegistration $registration)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:500'
        ]);

        $registration->update([
            'approval_status' => 'rejected',
            'approved_by' => auth('admin')->id(),
            'rejected_reason' => $request->rejected_reason,
        ]);

        return redirect()->back()->with('success', 'Registration rejected successfully.');
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id'
        ]);

        EventRegistration::whereIn('id', $request->registration_ids)
            ->update([
                'approval_status' => 'approved',
                'approved_by' => auth('admin')->id(),
                'approved_at' => now(),
            ]);

        return redirect()->back()->with('success', count($request->registration_ids) . ' registrations approved successfully.');
    }

    /**
     * Bulk reject registrations
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
            'rejected_reason' => 'required|string|max:500'
        ]);

        EventRegistration::whereIn('id', $request->registration_ids)
            ->update([
                'approval_status' => 'rejected',
                'approved_by' => auth('admin')->id(),
                'rejected_reason' => $request->rejected_reason,
            ]);

        return redirect()->back()->with('success', count($request->registration_ids) . ' registrations rejected successfully.');
    }
}
