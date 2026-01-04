<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JuryMapping;
use App\Models\User;
use App\Notifications\PresentationSelectedNotification;
use App\Notifications\PresentationRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PresentationSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show participants with their review scores for selection
     */
    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all participants with their average scores
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', 'participant')
            ->with(['user', 'juryMappingsAsParticipant.reviewerRegistration.user'])
            ->get()
            ->map(function($participant) {
                // Calculate average score from all reviewers
                $mappings = $participant->juryMappingsAsParticipant;
                
                $scores = $mappings->whereNotNull('score')->pluck('score');
                $participant->calculated_average = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;
                $participant->review_count = $mappings->count();
                $participant->completed_reviews = $mappings->where('status', 'completed')->count();
                
                return $participant;
            })
            ->sortByDesc('calculated_average');

        // Statistics
        $stats = [
            'total_participants' => $participants->count(),
            'reviewed_participants' => $participants->filter(fn($p) => $p->completed_reviews > 0)->count(),
            'pending_review' => $participants->filter(fn($p) => $p->completed_reviews == 0)->count(),
            'selected' => $participants->where('presentation_status', 'selected')->count(),
            'rejected' => $participants->where('presentation_status', 'rejected')->count(),
        ];

        return view('organizer.presentations.index', compact('event', 'participants', 'stats'));
    }

    /**
     * Select participant for presentation
     */
    public function select(Request $request, Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'presentation_queue' => 'nullable|integer|min:1',
            'presentation_time' => 'nullable|date',
            'presentation_link' => 'nullable|url',
            'presentation_location' => 'nullable|string|max:500',
        ]);

        // Calculate average score
        $scores = $participant->juryMappingsAsParticipant->whereNotNull('score')->pluck('score');
        $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;

        $participant->update([
            'presentation_status' => 'selected',
            'presentation_queue' => $request->presentation_queue,
            'presentation_time' => $request->presentation_time,
            'presentation_link' => $request->presentation_link,
            'presentation_location' => $request->presentation_location,
            'average_score' => $averageScore,
            'presentation_notified_at' => now(),
        ]);

        // Send notification to participant
        $participant->user->notify(new PresentationSelectedNotification($event, $participant));

        return back()->with('success', "{$participant->user->name} has been selected for presentation!");
    }

    /**
     * Reject participant from presenting
     */
    public function reject(Request $request, Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Calculate average score
        $scores = $participant->juryMappingsAsParticipant->whereNotNull('score')->pluck('score');
        $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;

        $participant->update([
            'presentation_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'average_score' => $averageScore,
            'presentation_notified_at' => now(),
        ]);

        // Send notification to participant
        $participant->user->notify(new PresentationRejectedNotification($event, $participant, $request->rejection_reason));

        return back()->with('success', "{$participant->user->name} has been rejected with notification sent.");
    }

    /**
     * Bulk select participants for presentation
     */
    public function bulkSelect(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:event_registrations,id',
            'auto_assign_queue' => 'boolean',
        ]);

        $participants = EventRegistration::whereIn('id', $request->participant_ids)
            ->where('event_id', $event->id)
            ->get();

        $queueNumber = 1;
        foreach ($participants as $participant) {
            $scores = $participant->juryMappingsAsParticipant->whereNotNull('score')->pluck('score');
            $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;

            $participant->update([
                'presentation_status' => 'selected',
                'presentation_queue' => $request->auto_assign_queue ? $queueNumber++ : null,
                'average_score' => $averageScore,
                'presentation_notified_at' => now(),
            ]);

            // Send notification
            $participant->user->notify(new PresentationSelectedNotification($event, $participant));
        }

        return back()->with('success', count($request->participant_ids) . ' participants selected for presentation!');
    }

    /**
     * Auto-select based on score threshold
     */
    public function autoSelect(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'min_score' => 'required|numeric|min:0|max:100',
            'auto_assign_queue' => 'boolean',
        ]);

        $participants = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', 'participant')
            ->whereNull('presentation_status')
            ->with('juryMappingsAsParticipant')
            ->get();

        $selected = 0;
        $queueNumber = EventRegistration::where('event_id', $event->id)
            ->where('presentation_status', 'selected')
            ->max('presentation_queue') ?? 0;
        $queueNumber++;

        foreach ($participants as $participant) {
            $scores = $participant->juryMappingsAsParticipant->whereNotNull('score')->pluck('score');
            
            if ($scores->isEmpty()) {
                continue; // Skip if no reviews
            }

            $averageScore = round($scores->avg(), 2);

            if ($averageScore >= $request->min_score) {
                $participant->update([
                    'presentation_status' => 'selected',
                    'presentation_queue' => $request->auto_assign_queue ? $queueNumber++ : null,
                    'average_score' => $averageScore,
                    'presentation_notified_at' => now(),
                ]);

                // Send notification
                $participant->user->notify(new PresentationSelectedNotification($event, $participant));
                $selected++;
            }
        }

        return back()->with('success', "{$selected} participants auto-selected based on score ≥ {$request->min_score}!");
    }

    /**
     * Update presentation details (queue, time, location, link)
     */
    public function updateDetails(Request $request, Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'presentation_queue' => 'nullable|integer|min:1',
            'presentation_time' => 'nullable|date',
            'presentation_link' => 'nullable|url',
            'presentation_location' => 'nullable|string|max:500',
        ]);

        $participant->update($request->only([
            'presentation_queue',
            'presentation_time',
            'presentation_link',
            'presentation_location',
        ]));

        return back()->with('success', 'Presentation details updated!');
    }

    /**
     * Resend notification to participant
     */
    public function resendNotification(Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        if ($participant->presentation_status === 'selected') {
            $participant->user->notify(new PresentationSelectedNotification($event, $participant));
        } elseif ($participant->presentation_status === 'rejected') {
            $participant->user->notify(new PresentationRejectedNotification($event, $participant, $participant->rejection_reason));
        } else {
            return back()->with('error', 'No notification to send for pending status');
        }

        $participant->update(['presentation_notified_at' => now()]);

        return back()->with('success', 'Notification resent to ' . $participant->user->name);
    }
}
