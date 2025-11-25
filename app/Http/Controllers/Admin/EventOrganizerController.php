<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use App\Notifications\EventOrganizerStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EventOrganizerController extends Controller
{
    /**
     * Display a listing of event organizers.
     */
    public function index()
    {
        $organizers = EventOrganizer::latest()->paginate(10);
        return view('admin.organizers.index', compact('organizers'));
    }

    /**
     * Show the form for creating a new event organizer.
     */
    public function create()
    {
        return view('admin.organizers.create');
    }

    /**
     * Store a newly created event organizer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|string|email|max:255|unique:event_organizers',
            'password' => 'required|string|min:8|confirmed',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_position' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['approved_by'] = Auth::id();
        $validated['approved_at'] = $validated['status'] === 'approved' ? now() : null;

        $organizer = EventOrganizer::create($validated);

        return redirect()->route('admin.organizers.show', $organizer)
            ->with('success', 'Event organizer created successfully.');
    }

    /**
     * Show event organizer details.
     */
    public function show(EventOrganizer $organizer)
    {
        return view('admin.organizers.show', compact('organizer'));
    }

    /**
     * Show the form for editing an event organizer.
     */
    public function edit(EventOrganizer $organizer)
    {
        return view('admin.organizers.edit', compact('organizer'));
    }

    /**
     * Update the specified event organizer.
     */
    public function update(Request $request, EventOrganizer $organizer)
    {
        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => ['required', 'string', 'email', 'max:255', Rule::unique('event_organizers')->ignore($organizer->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_position' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Set approval details if status changed to approved
        if ($validated['status'] === 'approved' && $organizer->status !== 'approved') {
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
            $validated['rejection_reason'] = null;
        }

        $organizer->update($validated);

        return redirect()->route('admin.organizers.show', $organizer)
            ->with('success', 'Event organizer updated successfully.');
    }

    /**
     * Remove the specified event organizer.
     */
    public function destroy(EventOrganizer $organizer)
    {
        $organizerName = $organizer->org_name;
        $organizer->delete();

        return redirect()->route('admin.organizers.index')
            ->with('success', "Event organizer '{$organizerName}' has been deleted successfully.");
    }

    /**
     * Approve an event organizer.
     */
    public function approve(EventOrganizer $organizer)
    {
        $organizer->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Event organizer has been approved successfully.'
            ]);
        }

        return redirect()->route('admin.organizers.show', $organizer)
            ->with('success', 'Event organizer has been approved successfully.');
    }

    /**
     * Reject an event organizer.
     */
    public function reject(Request $request, EventOrganizer $organizer)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $organizer->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Event organizer has been rejected successfully.'
            ]);
        }

        return redirect()->route('admin.organizers.show', $organizer)
            ->with('success', 'Event organizer has been rejected.');
    }
}
