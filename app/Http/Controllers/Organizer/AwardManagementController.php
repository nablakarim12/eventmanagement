<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAward;
use App\Models\ParticipantAward;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AwardManagementController extends Controller
{
    /**
     * Show award setup page (for configuring awards before assignment)
     */
    public function setup(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get or create mandatory awards
        $this->ensureMandatoryAwards($event);

        // Get all awards for this event
        $awards = EventAward::where('event_id', $event->id)
            ->orderBy('rank')
            ->orderBy('order')
            ->get();

        return view('organizer.awards.setup', compact('event', 'awards'));
    }

    /**
     * Show award management page for an event
     */
    public function index(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get or create mandatory awards
        $this->ensureMandatoryAwards($event);

        // Get all awards for this event
        $awards = EventAward::where('event_id', $event->id)
            ->orderBy('rank')
            ->orderBy('order')
            ->get();

        // Get all participant awards
        $participantAwards = ParticipantAward::where('event_id', $event->id)
            ->with(['user', 'award', 'registration'])
            ->get();

        return view('organizer.awards.index', compact('event', 'awards', 'participantAwards'));
    }

    /**
     * Ensure mandatory awards (Gold, Silver, Bronze) exist
     */
    private function ensureMandatoryAwards(Event $event)
    {
        $mandatoryAwards = [
            ['name' => 'Gold Medal', 'rank' => 1, 'color' => '#FFD700', 'order' => 1],
            ['name' => 'Silver Medal', 'rank' => 2, 'color' => '#C0C0C0', 'order' => 2],
            ['name' => 'Bronze Medal', 'rank' => 3, 'color' => '#CD7F32', 'order' => 3],
        ];

        foreach ($mandatoryAwards as $awardData) {
            // Check if award already exists
            $exists = EventAward::where('event_id', $event->id)
                ->where('name', $awardData['name'])
                ->exists();

            if (!$exists) {
                EventAward::create([
                    'event_id' => $event->id,
                    'name' => $awardData['name'],
                    'rank' => $awardData['rank'],
                    'award_type' => 'standard',
                    'is_mandatory' => DB::raw('true'), // PostgreSQL boolean
                    'color' => $awardData['color'],
                    'order' => $awardData['order'],
                ]);
            }
        }
    }

    /**
     * Add custom award
     */
    public function storeAward(Request $request, Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'rank' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        EventAward::create([
            'event_id' => $event->id,
            'name' => $request->name,
            'display_name' => $request->display_name,
            'rank' => $request->rank,
            'award_type' => 'custom',
            'is_mandatory' => DB::raw('false'),
            'description' => $request->description,
            'color' => $request->color,
            'order' => EventAward::where('event_id', $event->id)->max('order') + 1,
        ]);

        return back()->with('success', 'Custom award added successfully!');
    }

    /**
     * Delete custom award
     */
    public function deleteAward(Event $event, EventAward $award)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deleting mandatory awards
        if ($award->is_mandatory) {
            return back()->with('error', 'Cannot delete mandatory awards (Gold, Silver, Bronze).');
        }

        $award->delete();

        return back()->with('success', 'Award deleted successfully!');
    }

    /**
     * Auto-suggest rankings based on scores
     */
    public function autoSuggestRankings(Request $request, Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'ranking_scope' => 'required|in:category_theme,category,theme,overall',
        ]);

        $rankingScope = $request->ranking_scope;

        // Get all participants with their scores
        $participants = DB::table('event_papers as ep')
            ->join('users as u', 'ep.user_id', '=', 'u.id')
            ->join('event_registrations as er', function($join) use ($event) {
                $join->on('er.user_id', '=', 'ep.user_id')
                     ->where('er.event_id', '=', $event->id);
            })
            ->where('ep.event_id', $event->id)
            ->select(
                'ep.id as paper_id',
                'ep.user_id',
                'er.id as registration_id',
                'u.name',
                'er.selected_category as category',
                DB::raw('NULL as theme'), // Will update when theme column exists
                DB::raw('0 as avg_score')
            )
            ->get();

        // Calculate average scores for each participant
        foreach ($participants as $participant) {
            $avgScore = DB::table('rubric_item_scores')
                ->where('event_paper_id', $participant->paper_id)
                ->avg('score');
            
            $participant->avg_score = round($avgScore ?? 0, 2);
        }

        // Group and rank based on scope
        $rankings = $this->calculateRankings($participants, $rankingScope);

        return response()->json([
            'success' => true,
            'rankings' => $rankings,
            'scope' => $rankingScope,
        ]);
    }

    /**
     * Calculate rankings based on scope
     */
    private function calculateRankings($participants, $scope)
    {
        $rankings = [];

        switch ($scope) {
            case 'category_theme':
                // Group by category + theme
                $grouped = $participants->groupBy(function($p) {
                    return $p->category . '|' . $p->theme;
                });
                
                foreach ($grouped as $key => $group) {
                    $sorted = $group->sortByDesc('avg_score')->values();
                    $rankings[$key] = $sorted->map(function($p, $index) {
                        return [
                            'user_id' => $p->user_id,
                            'registration_id' => $p->registration_id,
                            'name' => $p->name,
                            'score' => $p->avg_score,
                            'rank' => $index + 1,
                            'category' => $p->category,
                            'theme' => $p->theme,
                        ];
                    });
                }
                break;

            case 'category':
                // Group by category only
                $grouped = $participants->groupBy('category');
                
                foreach ($grouped as $category => $group) {
                    $sorted = $group->sortByDesc('avg_score')->values();
                    $rankings[$category] = $sorted->map(function($p, $index) {
                        return [
                            'user_id' => $p->user_id,
                            'registration_id' => $p->registration_id,
                            'name' => $p->name,
                            'score' => $p->avg_score,
                            'rank' => $index + 1,
                            'category' => $p->category,
                            'theme' => $p->theme,
                        ];
                    });
                }
                break;

            case 'theme':
                // Group by theme only
                $grouped = $participants->groupBy('theme');
                
                foreach ($grouped as $theme => $group) {
                    $sorted = $group->sortByDesc('avg_score')->values();
                    $rankings[$theme] = $sorted->map(function($p, $index) {
                        return [
                            'user_id' => $p->user_id,
                            'registration_id' => $p->registration_id,
                            'name' => $p->name,
                            'score' => $p->avg_score,
                            'rank' => $index + 1,
                            'category' => $p->category,
                            'theme' => $p->theme,
                        ];
                    });
                }
                break;

            case 'overall':
                // All participants together
                $sorted = $participants->sortByDesc('avg_score')->values();
                $rankings['overall'] = $sorted->map(function($p, $index) {
                    return [
                        'user_id' => $p->user_id,
                        'registration_id' => $p->registration_id,
                        'name' => $p->name,
                        'score' => $p->avg_score,
                        'rank' => $index + 1,
                        'category' => $p->category,
                        'theme' => $p->theme,
                    ];
                });
                break;
        }

        return $rankings;
    }

    /**
     * Assign award to participant
     */
    public function assignAward(Request $request, Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'registration_id' => 'required|exists:event_registrations,id',
            'award_id' => 'required|exists:event_awards,id',
            'final_score' => 'nullable|numeric|min:0|max:100',
            'category' => 'nullable|string',
            'theme' => 'nullable|string',
            'ranking_scope' => 'required|in:category_theme,category,theme,overall',
            'rank' => 'required|integer|min:1',
        ]);

        ParticipantAward::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $request->user_id,
                'ranking_scope' => $request->ranking_scope,
            ],
            [
                'event_award_id' => $request->award_id,
                'registration_id' => $request->registration_id,
                'final_score' => $request->final_score,
                'category' => $request->category,
                'theme' => $request->theme,
                'rank' => $request->rank,
                'is_published' => false,
            ]
        );

        return back()->with('success', 'Award assigned successfully!');
    }

    /**
     * Publish all awards
     */
    public function publishAwards(Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        ParticipantAward::where('event_id', $event->id)
            ->update(['is_published' => true]);

        return back()->with('success', 'All awards have been published! Participants can now see their results.');
    }

    /**
     * Unpublish awards
     */
    public function unpublishAwards(Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        ParticipantAward::where('event_id', $event->id)
            ->update(['is_published' => false]);

        return back()->with('success', 'Awards have been hidden from participants.');
    }

    /**
     * Assign multiple awards to a participant
     */
    public function assignMultipleAwards(Request $request, Event $event)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'paper_id' => 'required|exists:event_papers,id',
            'award_ids' => 'required|array',
            'award_ids.*' => 'exists:event_awards,id',
        ]);

        // Get the participant's registration
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        // Get the participant's score
        $avgScore = DB::table('rubric_item_scores')
            ->where('event_paper_id', $validated['paper_id'])
            ->avg('score');

        // Remove existing awards for this participant
        ParticipantAward::where('event_id', $event->id)
            ->where('user_id', $validated['user_id'])
            ->delete();

        // Assign new awards
        foreach ($validated['award_ids'] as $awardId) {
            $award = EventAward::find($awardId);
            
            ParticipantAward::create([
                'event_id' => $event->id,
                'event_award_id' => $awardId,
                'user_id' => $validated['user_id'],
                'registration_id' => $registration->id,
                'final_score' => round($avgScore ?? 0, 2),
                'ranking_scope' => $registration->selected_category ?? 'overall',
                'is_published' => DB::raw('false'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Awards assigned successfully',
            'count' => count($validated['award_ids']),
        ]);
    }

    /**
     * Get awards for a specific participant
     */
    public function getParticipantAwards(Event $event, $userId)
    {
        if ($event->organizer_id !== Auth::id()) {
            abort(403);
        }

        $awardIds = ParticipantAward::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->pluck('event_award_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'awards' => $awardIds,
        ]);
    }
}
