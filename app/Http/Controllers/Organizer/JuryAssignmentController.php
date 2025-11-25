<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JuryAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JuryAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show jury assignment page for an event
     */
    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Get all checked-in participants (excluding jury/both roles)
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->where('role', 'participant')
            ->with(['user', 'juryAssignments.juryRegistration.user'])
            ->orderBy('checked_in_at')
            ->get();

        // Get all checked-in jury members
        $juryMembers = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->whereIn('role', ['jury', 'both'])
            ->with(['user'])
            ->orderBy('checked_in_at')
            ->get();

        // Statistics
        $stats = [
            'total_participants' => $participants->count(),
            'total_jury' => $juryMembers->count(),
            'participants_with_jury' => $participants->filter(function($p) {
                return $p->juryAssignments->count() > 0;
            })->count(),
            'total_assignments' => JuryAssignment::whereHas('participantRegistration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->count(),
        ];

        return view('organizer.jury.assignments.index', compact('event', 'participants', 'juryMembers', 'stats'));
    }

    /**
     * Assign jury to a participant
     */
    public function assign(Request $request, Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'jury_registration_id' => 'required|exists:event_registrations,id',
        ]);

        // Verify jury registration is valid
        $juryRegistration = EventRegistration::where('id', $request->jury_registration_id)
            ->where('event_id', $event->id)
            ->whereIn('role', ['jury', 'both'])
            ->whereNotNull('checked_in_at')
            ->firstOrFail();

        // PREVENT SELF-EVALUATION: Check if jury and participant are the same user
        if ($juryRegistration->user_id === $participant->user_id) {
            return back()->with('error', 'A user cannot evaluate their own product/presentation.');
        }

        // Check if already assigned
        $exists = JuryAssignment::where('participant_registration_id', $participant->id)
            ->where('jury_registration_id', $juryRegistration->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This jury member is already assigned to this participant.');
        }

        // Create assignment
        JuryAssignment::create([
            'participant_registration_id' => $participant->id,
            'jury_registration_id' => $juryRegistration->id,
            'assigned_by' => $organizer->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Jury member assigned successfully.');
    }

    /**
     * Assign multiple jury members to a participant
     */
    public function assignMultiple(Request $request, Event $event, EventRegistration $participant)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'jury_ids' => 'required|array|min:1',
            'jury_ids.*' => 'exists:event_registrations,id',
        ]);

        $assignedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->jury_ids as $juryId) {
                // Verify jury registration
                $juryRegistration = EventRegistration::where('id', $juryId)
                    ->where('event_id', $event->id)
                    ->whereIn('role', ['jury', 'both'])
                    ->whereNotNull('checked_in_at')
                    ->first();

                if (!$juryRegistration) {
                    $skippedCount++;
                    continue;
                }

                // PREVENT SELF-EVALUATION: Skip if same user
                if ($juryRegistration->user_id === $participant->user_id) {
                    $skippedCount++;
                    continue;
                }

                // Check if already assigned
                $exists = JuryAssignment::where('participant_registration_id', $participant->id)
                    ->where('jury_registration_id', $juryRegistration->id)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Create assignment
                JuryAssignment::create([
                    'participant_registration_id' => $participant->id,
                    'jury_registration_id' => $juryRegistration->id,
                    'assigned_by' => $organizer->id,
                    'status' => 'pending',
                ]);

                $assignedCount++;
            }

            DB::commit();

            $message = "Assigned {$assignedCount} jury member(s) successfully.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} skipped - already assigned, invalid, or self-evaluation prevented)";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign jury members: ' . $e->getMessage());
        }
    }

    /**
     * Remove jury assignment
     */
    public function remove(Event $event, JuryAssignment $assignment)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Verify assignment belongs to this event
        $participant = $assignment->participantRegistration;
        if ($participant->event_id !== $event->id) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Jury assignment removed successfully.');
    }

    /**
     * Auto-assign jury members (distribute evenly)
     */
    public function autoAssign(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'jury_per_participant' => 'required|integer|min:1|max:10',
        ]);

        $juryPerParticipant = $request->jury_per_participant;

        // Get all participants
        $participants = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->where('role', 'participant')
            ->with('juryAssignments')
            ->get();

        // Get all available jury members
        $juryMembers = EventRegistration::where('event_id', $event->id)
            ->where('approval_status', 'approved')
            ->whereNotNull('checked_in_at')
            ->whereIn('role', ['jury', 'both'])
            ->get();

        if ($juryMembers->count() === 0) {
            return back()->with('error', 'No jury members available for assignment.');
        }

        $assignedCount = 0;
        $juryIndex = 0;

        DB::beginTransaction();
        try {
            foreach ($participants as $participant) {
                $currentAssignments = $participant->juryAssignments->count();
                $needed = $juryPerParticipant - $currentAssignments;

                // Skip if this participant already has enough jury
                if ($needed <= 0) {
                    continue;
                }

                // Get already assigned jury IDs for this participant
                $alreadyAssigned = $participant->juryAssignments->pluck('jury_registration_id')->toArray();

                for ($i = 0; $i < $needed; $i++) {
                    $attempts = 0;
                    while ($attempts < $juryMembers->count()) {
                        $jury = $juryMembers[$juryIndex % $juryMembers->count()];
                        $juryIndex++;

                        // PREVENT SELF-EVALUATION: Skip if same user
                        if ($jury->user_id === $participant->user_id) {
                            $attempts++;
                            continue;
                        }

                        // Skip if already assigned to this participant
                        if (in_array($jury->id, $alreadyAssigned)) {
                            $attempts++;
                            continue;
                        }

                        // Create assignment
                        JuryAssignment::create([
                            'participant_registration_id' => $participant->id,
                            'jury_registration_id' => $jury->id,
                            'assigned_by' => $organizer->id,
                            'status' => 'pending',
                        ]);

                        $alreadyAssigned[] = $jury->id;
                        $assignedCount++;
                        break;
                    }
                }
            }

            DB::commit();

            return back()->with('success', "Auto-assigned {$assignedCount} jury member(s) successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Auto-assignment failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear all assignments for an event
     */
    public function clearAll(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $count = JuryAssignment::whereHas('participantRegistration', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->delete();

        return back()->with('success', "Cleared {$count} jury assignment(s).");
    }
}
