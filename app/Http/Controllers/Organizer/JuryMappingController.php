<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JuryAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JuryMappingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show jury mapping overview for all events
     */
    public function index()
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get all events with their jury assignment statistics
        $events = Event::where('organizer_id', $organizer->id)
            ->whereIn('status', ['published', 'ongoing'])
            ->where('start_date', '>=', now()->subDays(7)) // Events from last 7 days onwards
            ->with(['registrations' => function($q) {
                $q->where('approval_status', 'approved')
                  ->whereNotNull('checked_in_at');
            }])
            ->latest('start_date')
            ->get()
            ->map(function($event) {
                // Count participants and jury (including "both" role)
                $participants = $event->registrations->whereIn('role', ['participant', 'both']);
                $jury = $event->registrations->whereIn('role', ['jury', 'both']);
                
                // Count assignments
                $totalAssignments = JuryAssignment::whereHas('participantRegistration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->count();
                
                // Count participants with at least one jury
                $participantsWithJury = JuryAssignment::whereHas('participantRegistration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->distinct('participant_registration_id')
                ->count('participant_registration_id');
                
                $event->participants_count = $participants->count();
                $event->jury_count = $jury->count();
                $event->total_assignments = $totalAssignments;
                $event->participants_with_jury = $participantsWithJury;
                $event->participants_without_jury = $participants->count() - $participantsWithJury;
                $event->mapping_percentage = $participants->count() > 0 
                    ? round(($participantsWithJury / $participants->count()) * 100, 1) 
                    : 0;
                
                return $event;
            })
            ->filter(function($event) {
                // Only show events that have at least participants or jury
                return $event->participants_count > 0 || $event->jury_count > 0;
            })
            ->values(); // Reset array keys after filtering

        return view('organizer.jury-mapping.index', compact('events'));
    }

    /**
     * Show detailed jury mapping for a specific event
     */
    public function show(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all participants with their jury assignments (including 'both' role)
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->whereIn('role', ['participant', 'both'])
            ->with(['user', 'juryAssignmentsAsParticipant.juryRegistration.user'])
            ->orderBy('checked_in_at')
            ->get();

        // Get jury workload - each jury member with their assignment count
        $juryWorkload = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->whereIn('role', ['jury', 'both'])
            ->with(['user'])
            ->withCount(['juryAssignmentsAsJury as participants_count' => function($query) {
                $query->whereNotNull('participant_registration_id');
            }])
            ->get();

        // Statistics
        $stats = [
            'total_participants' => $participants->count(),
            'total_jury' => $juryWorkload->count(),
            'participants_with_jury' => $participants->filter(function($p) {
                return $p->juryAssignmentsAsParticipant->count() > 0;
            })->count(),
            'participants_without_jury' => $participants->filter(function($p) {
                return $p->juryAssignmentsAsParticipant->count() === 0;
            })->count(),
            'total_assignments' => JuryAssignment::whereHas('participantRegistration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->count(),
            'avg_jury_per_participant' => $participants->count() > 0 
                ? round($participants->sum(function($p) { return $p->juryAssignmentsAsParticipant->count(); }) / $participants->count(), 1)
                : 0,
        ];

        return view('organizer.jury-mapping.show', compact('event', 'participants', 'juryWorkload', 'stats'));
    }
}
