<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JuryMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JuryMappingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show jury mapping overview for all conference events
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get event type filter from request
        $eventType = $request->query('type'); // 'innovation' or 'conference'
        
        // Get events with approved registrations
        $query = Event::where('organizer_id', $organizer->id);
        
        // Filter by event type
        if ($eventType === 'innovation') {
            $query->whereHas('category', function($q) {
                $q->where('name', 'Innovation Competition');
            });
        } elseif ($eventType === 'conference') {
            $query->whereHas('category', function($q) {
                $q->where('name', '!=', 'Innovation Competition');
            });
        } else {
            $query->whereHas('category', function($q) {
                $q->where('name', '!=', 'Innovation Competition');
            }); // Default to conference events for backward compatibility
        }
        
        $events = $query->whereIn('status', ['published'])
            ->with(['registrations' => function($q) {
                $q->where('status', 'confirmed');
            }])
            ->latest('created_at')
            ->get()
            ->map(function($event) {
                // Determine event type based on category
                $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';
                $juryRole = $eventType === 'innovation' ? 'jury' : 'reviewer';
                
                // Count participants and reviewers
                $participants = $event->registrations->where('role', 'participant');
                $reviewers = $event->registrations->where('role', $juryRole);
                
                // Count mappings
                $totalMappings = JuryMapping::where('event_id', $event->id)->count();
                
                // Count participants with at least one reviewer assigned
                $participantsWithReviewer = JuryMapping::where('event_id', $event->id)
                    ->distinct('participant_registration_id')
                    ->count('participant_registration_id');
                
                $event->participants_count = $participants->count();
                $event->reviewers_count = $reviewers->count();
                $event->total_mappings = $totalMappings;
                $event->participants_with_reviewer = $participantsWithReviewer;
                $event->participants_without_reviewer = $participants->count() - $participantsWithReviewer;
                $event->mapping_percentage = $participants->count() > 0 
                    ? round(($participantsWithReviewer / $participants->count()) * 100, 1) 
                    : 0;
                
                return $event;
            })
            ->filter(function($event) {
                // Only show events that have participants or reviewers
                return $event->participants_count > 0 || $event->reviewers_count > 0;
            })
            ->values();

        return view('organizer.jury-mapping.index', compact('events', 'eventType'));
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
        
        // Determine event type based on category
        $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';
        $juryRole = $eventType === 'innovation' ? 'jury' : 'reviewer';

        // Get all participants with their reviewer assignments
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', 'participant')
            ->with(['user', 'juryMappingsAsParticipant.reviewerRegistration.user'])
            ->orderBy('created_at')
            ->get();

        // Get reviewer workload - each reviewer with their assignment count
        $reviewerWorkload = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', $juryRole)
            ->with(['user'])
            ->withCount(['juryMappingsAsReviewer as participants_assigned'])
            ->get()
            ->map(function($reviewer) {
                $reviewer->workload_status = $this->getWorkloadStatus($reviewer->participants_assigned);
                return $reviewer;
            });

        $stats = [
            'total_participants' => $participants->count(),
            'total_reviewers' => $reviewerWorkload->count(),
            'participants_with_reviewer' => $participants->filter(function($p) {
                return $p->juryMappingsAsParticipant->count() > 0;
            })->count(),
            'total_mappings' => JuryMapping::where('event_id', $event->id)->count(),
        ];

        return view('organizer.jury-mapping.show', compact('event', 'participants', 'reviewerWorkload', 'stats', 'eventType'));
    }

    /**
     * Get eligible reviewers for a participant (with category constraint)
     */
    public function getEligibleReviewers(Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Determine event type and role
        $eventType = $event->delivery_mode ? 'conference' : 'innovation';
        $juryRole = $eventType === 'innovation' ? 'jury' : 'reviewer';

        // Get all reviewers for this event
        $eligibleReviewers = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', $juryRole)
            ->where('user_id', '!=', $participant->user_id) // CONSTRAINT: Cannot be the same user
            ->where(function($query) use ($participant) {
                // CONSTRAINT: Reviewer cannot evaluate participant from same category
                if ($participant->selected_category) {
                    $query->where('selected_category', '!=', $participant->selected_category)
                          ->orWhereNull('selected_category');
                }
            })
            ->where(function($query) use ($participant, $event) {
                // CONSTRAINT: For Innovation events, reviewer cannot evaluate participant from same theme
                if (!$event->delivery_mode) { // Innovation event
                    $participantPaper = \App\Models\EventPaper::where('event_id', $participant->event_id)
                        ->where('user_id', $participant->user_id)
                        ->first();
                    
                    if ($participantPaper && $participantPaper->product_theme) {
                        $query->whereDoesntHave('user.eventPapers', function($q) use ($participant, $participantPaper) {
                            $q->where('event_id', $participant->event_id)
                              ->where('product_theme', $participantPaper->product_theme);
                        });
                    }
                }
            })
            ->whereNotIn('id', function($query) use ($participant) {
                // Exclude already assigned reviewers
                $query->select('jury_registration_id')
                    ->from('jury_mappings')
                    ->where('participant_registration_id', $participant->id);
            })
            ->with(['user'])
            ->withCount(['juryMappingsAsReviewer as current_workload'])
            ->get()
            ->map(function($reviewer) {
                $reviewer->workload_status = $this->getWorkloadStatus($reviewer->current_workload);
                return $reviewer;
            })
            ->sortBy('current_workload'); // Show least loaded reviewers first

        return response()->json($eligibleReviewers);
    }

    /**
     * Assign reviewer to participant
     */
    public function assignReviewer(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'participant_registration_id' => 'required|exists:event_registrations,id',
            'reviewer_registration_id' => 'required|exists:event_registrations,id',
        ]);

        $participant = EventRegistration::findOrFail($request->participant_registration_id);
        $reviewer = EventRegistration::findOrFail($request->reviewer_registration_id);

        // Validation: Same person check (if user is both participant and reviewer)
        if ($participant->user_id === $reviewer->user_id) {
            return response()->json(['error' => 'User cannot review their own paper!'], 422);
        }

        // Validation: Same category check
        if ($participant->selected_category && $reviewer->selected_category && 
            $participant->selected_category === $reviewer->selected_category) {
            return response()->json([
                'error' => "Reviewer from '{$reviewer->selected_category}' category cannot evaluate participant from the same category!"
            ], 422);
        }

        // Validation: Same theme check (Innovation events only)
        $event = $participant->event;
        if (!$event->delivery_mode) { // Innovation event
            $participantPaper = \App\Models\EventPaper::where('event_id', $participant->event_id)
                ->where('user_id', $participant->user_id)
                ->first();
            $reviewerPaper = \App\Models\EventPaper::where('event_id', $reviewer->event_id)
                ->where('user_id', $reviewer->user_id)
                ->first();
            
            if ($participantPaper && $reviewerPaper && 
                $participantPaper->product_theme && $reviewerPaper->product_theme &&
                $participantPaper->product_theme === $reviewerPaper->product_theme) {
                return response()->json([
                    'error' => "Jury from Theme {$reviewerPaper->product_theme} cannot evaluate participant from the same theme!"
                ], 422);
            }
        }

        // Check if already assigned
        $existing = JuryMapping::where('jury_registration_id', $reviewer->id)
            ->where('participant_registration_id', $participant->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'This reviewer is already assigned to this participant'], 422);
        }

        // Create mapping
        $mapping = JuryMapping::create([
            'event_id' => $participant->event_id,
            'jury_registration_id' => $reviewer->id,
            'participant_registration_id' => $participant->id,
            'assigned_by' => $organizer->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reviewer assigned successfully',
            'mapping' => $mapping->load('reviewerRegistration.user'),
        ]);
    }

    /**
     * Remove reviewer from participant
     */
    public function removeReviewer(JuryMapping $mapping)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($mapping->assigned_by !== $organizer->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mapping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reviewer removed successfully',
        ]);
    }

    /**
     * Auto-assign reviewers to all participants
     */
    public function autoAssign(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            return back()->with('error', 'Unauthorized');
        }

        // Determine event type and role
        $eventType = $event->delivery_mode ? 'conference' : 'innovation';
        $juryRole = $eventType === 'innovation' ? 'jury' : 'reviewer';

        $participants = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', 'participant')
            ->get();

        $reviewers = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('role', $juryRole)
            ->get();

        $assigned = 0;
        $minReviewersPerParticipant = 2; // Configurable

        foreach ($participants as $participant) {
            // Get eligible reviewers for this participant
            $eligible = $reviewers->filter(function($reviewer) use ($participant, $event) {
                // Cannot be same user
                if ($reviewer->user_id === $participant->user_id) {
                    return false;
                }
                
                // Cannot be same category
                if ($participant->selected_category && $reviewer->selected_category && 
                    $participant->selected_category === $reviewer->selected_category) {
                    return false;
                }
                
                // Cannot be same theme (Innovation events only)
                if (!$event->delivery_mode) { // Innovation event
                    $participantPaper = \App\Models\EventPaper::where('event_id', $participant->event_id)
                        ->where('user_id', $participant->user_id)
                        ->first();
                    $reviewerPaper = \App\Models\EventPaper::where('event_id', $reviewer->event_id)
                        ->where('user_id', $reviewer->user_id)
                        ->first();
                    
                    if ($participantPaper && $reviewerPaper && 
                        $participantPaper->product_theme && $reviewerPaper->product_theme &&
                        $participantPaper->product_theme === $reviewerPaper->product_theme) {
                        return false;
                    }
                }
                
                // Check if already assigned
                $alreadyAssigned = JuryMapping::where('participant_registration_id', $participant->id)
                    ->where('jury_registration_id', $reviewer->id)
                    ->exists();
                
                return !$alreadyAssigned;
            });

            // Assign reviewers (balanced distribution)
            $currentAssignments = JuryMapping::where('participant_registration_id', $participant->id)->count();
            $needed = max(0, $minReviewersPerParticipant - $currentAssignments);

            if ($needed > 0 && $eligible->count() >= $needed) {
                // Sort by current workload
                $eligible = $eligible->sortBy(function($reviewer) {
                    return JuryMapping::where('jury_registration_id', $reviewer->id)->count();
                });

                foreach ($eligible->take($needed) as $reviewer) {
                    JuryMapping::create([
                        'event_id' => $event->id,
                        'jury_registration_id' => $reviewer->id,
                        'participant_registration_id' => $participant->id,
                        'assigned_by' => $organizer->id,
                        'status' => 'pending',
                    ]);
                    $assigned++;
                }
            }
        }

        return back()->with('success', "Auto-assigned {$assigned} reviewer mappings successfully!");
    }

    /**
     * Helper: Get workload status color
     */
    private function getWorkloadStatus($count)
    {
        if ($count == 0) return 'light';
        if ($count <= 3) return 'good';
        if ($count <= 6) return 'moderate';
        return 'heavy';
    }
}
