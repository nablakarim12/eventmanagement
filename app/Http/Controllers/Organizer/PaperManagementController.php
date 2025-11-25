<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\PaperSubmission;
use App\Models\JuryAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaperManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * List all papers for an event
     */
    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $papers = PaperSubmission::where('event_id', $event->id)
            ->with(['user', 'authors', 'juryAssignments.juryRegistration.user'])
            ->latest()
            ->get();

        return view('organizer.papers.index', compact('event', 'papers'));
    }

    /**
     * Show a specific paper with details
     */
    public function show(Event $event, PaperSubmission $paper)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $paper->event_id !== $event->id) {
            abort(403);
        }

        $paper->load([
            'user',
            'authors',
            'juryAssignments.juryRegistration.user',
            'reviews.juryRegistration.user'
        ]);

        // Get available jury members for this event
        $availableJury = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereIn('role', ['jury', 'both'])
            ->whereNotNull('checked_in_at') // Only checked-in jury
            ->with('user')
            ->get();

        return view('organizer.papers.show', compact('event', 'paper', 'availableJury'));
    }

    /**
     * Assign jury to a paper
     */
    public function assignJury(Request $request, Event $event, PaperSubmission $paper)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $paper->event_id !== $event->id) {
            abort(403);
        }

        $request->validate([
            'jury_registration_ids' => 'required|array|min:1',
            'jury_registration_ids.*' => 'exists:event_registrations,id',
        ]);

        $assignedCount = 0;

        foreach ($request->jury_registration_ids as $juryRegistrationId) {
            // Check if already assigned
            $exists = JuryAssignment::where('paper_submission_id', $paper->id)
                ->where('jury_registration_id', $juryRegistrationId)
                ->exists();

            if (!$exists) {
                JuryAssignment::create([
                    'paper_submission_id' => $paper->id,
                    'jury_registration_id' => $juryRegistrationId,
                    'assigned_by' => $organizer->id,
                    'status' => 'pending',
                ]);
                $assignedCount++;
            }
        }

        // Update paper status
        if ($paper->status === 'submitted') {
            $paper->status = 'under_review';
            $paper->save();
        }

        return back()->with('success', "Successfully assigned {$assignedCount} jury member(s) to this paper.");
    }

    /**
     * Remove jury assignment
     */
    public function removeJury(Event $event, PaperSubmission $paper, JuryAssignment $assignment)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || 
            $paper->event_id !== $event->id || 
            $assignment->paper_submission_id !== $paper->id) {
            abort(403);
        }

        // Only remove if review not yet submitted
        if ($assignment->review && $assignment->review->status === 'submitted') {
            return back()->with('error', 'Cannot remove jury who has already submitted a review.');
        }

        $assignment->delete();

        return back()->with('success', 'Jury assignment removed.');
    }

    /**
     * Download paper PDF
     */
    public function download(Event $event, PaperSubmission $paper)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $paper->event_id !== $event->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($paper->paper_file_path)) {
            return back()->with('error', 'Paper file not found.');
        }

        return Storage::disk('public')->download($paper->paper_file_path, $paper->paper_file_name);
    }

    /**
     * Update paper status (accept/reject)
     */
    public function updateStatus(Request $request, Event $event, PaperSubmission $paper)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $paper->event_id !== $event->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'required_if:status,rejected',
        ]);

        $paper->status = $request->status;
        $paper->rejection_reason = $request->rejection_reason;
        $paper->reviewed_at = now();
        $paper->save();

        return back()->with('success', 'Paper status updated successfully.');
    }
}
