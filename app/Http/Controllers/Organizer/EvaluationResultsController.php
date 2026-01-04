<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAward;
use App\Models\ParticipantAward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationResultsController extends Controller
{
    /**
     * Display list of events with evaluation results
     */
    public function index(Request $request)
    {
        // Get event type filter from request
        $eventType = $request->query('type'); // 'innovation' or 'conference'
        
        $query = Event::where('organizer_id', Auth::id());
        
        // Filter by event type
        if ($eventType === 'innovation') {
            $query->whereHas('category', function($q) {
                $q->where('name', 'Innovation Competition');
            });
        } elseif ($eventType === 'conference') {
            $query->whereHas('category', function($q) {
                $q->where('name', '!=', 'Innovation Competition');
            });
        }
        
        $events = $query->orderBy('created_at', 'desc')
            ->get();

        // Get evaluation counts for each event
        foreach ($events as $event) {
            $event->evaluation_count = DB::table('rubric_item_scores')
                ->join('event_papers', 'rubric_item_scores.event_paper_id', '=', 'event_papers.id')
                ->where('event_papers.event_id', $event->id)
                ->distinct('rubric_item_scores.event_paper_id')
                ->count('rubric_item_scores.event_paper_id');
        }

        return view('organizer.evaluation-results.index', compact('events', 'eventType'));
    }

    /**
     * Show detailed evaluation results for a specific event
     */
    public function showEvent(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Determine event type based on category
        $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';

        // Get all papers for this event with their scores and presentation status
        $papers = DB::table('event_papers as ep')
            ->leftJoin('users as u', 'ep.user_id', '=', 'u.id')
            ->leftJoin('event_registrations as er', function($join) use ($event) {
                $join->on('er.user_id', '=', 'ep.user_id')
                     ->where('er.event_id', '=', $event->id);
            })
            ->where('ep.event_id', $event->id)
            ->select(
                'ep.*', 
                'u.name as author_name', 
                'u.email as author_email', 
                'er.presentation_status',
                'er.selected_category'
            )
            ->get();

        foreach ($papers as $paper) {
            // Get rubric scores for this participant/paper
            // For Innovation: $paper->id is registration ID, but scores are stored against event_paper_id
            // Need to check if there's a corresponding "paper" record or if we use registration ID directly
            $scores = DB::table('rubric_item_scores as ris')
                ->join('rubric_items as ri', 'ris.rubric_item_id', '=', 'ri.id')
                ->join('rubric_categories as rc', 'ri.rubric_category_id', '=', 'rc.id')
                ->where('ris.event_paper_id', $paper->id)
                ->select(
                    'rc.id as category_id',
                    'rc.name as category_name',
                    'rc.max_score as category_max_score',
                    'ri.id as item_id',
                    'ri.name as item_name',
                    'ri.max_score as item_max_score',
                    DB::raw('AVG(ris.score) as avg_score'),
                    DB::raw('COUNT(DISTINCT ris.evaluator_id) as evaluator_count')
                )
                ->groupBy('rc.id', 'rc.name', 'rc.max_score', 'ri.id', 'ri.name', 'ri.max_score')
                ->orderBy('rc.order')
                ->orderBy('ri.order')
                ->get();

            // Group scores by category
            $categorizedScores = $scores->groupBy('category_id');
            
            $paper->scores = $categorizedScores;
            $paper->total_score = 0;
            $paper->total_max_score = 0;
            
            foreach ($categorizedScores as $categoryId => $categoryScores) {
                $categoryTotal = $categoryScores->sum('avg_score');
                $categoryMax = $categoryScores->first()->category_max_score;
                
                $paper->total_score += $categoryTotal;
                $paper->total_max_score += $categoryMax;
            }

            // Get evaluator details
            $paper->evaluators = DB::table('rubric_item_scores')
                ->join('users', 'rubric_item_scores.evaluator_id', '=', 'users.id')
                ->where('rubric_item_scores.event_paper_id', $paper->id)
                ->select('users.id', 'users.name', 'users.email')
                ->distinct()
                ->get();
        }

        // Get rubric categories for the event
        $categories = DB::table('rubric_categories')
            ->where('event_id', $event->id)
            ->orderBy('order')
            ->get();

        // For innovation events, get award information
        $awards = null;
        $participantAwards = null;
        if ($eventType === 'innovation') {
            $awards = \App\Models\EventAward::where('event_id', $event->id)
                ->orderBy('rank')
                ->get();
            $participantAwards = \App\Models\ParticipantAward::where('event_id', $event->id)
                ->with('award')
                ->get()
                ->groupBy('user_id');
        }

        return view('organizer.evaluation-results.show-event', compact('event', 'papers', 'categories', 'eventType', 'awards', 'participantAwards'));
    }

    /**
     * Show detailed jury scores for a specific paper
     */
    public function showPaperDetails(Event $event, $paperId)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Determine event type based on category
        $eventType = ($event->category && $event->category->name === 'Innovation Competition') ? 'innovation' : 'conference';

        // Get paper details with registration info
        $paper = DB::table('event_papers as ep')
            ->leftJoin('users as u', 'ep.user_id', '=', 'u.id')
            ->leftJoin('event_registrations as er', function($join) use ($event) {
                $join->on('er.user_id', '=', 'ep.user_id')
                     ->where('er.event_id', '=', $event->id);
            })
            ->where('ep.id', $paperId)
            ->where('ep.event_id', $event->id)
            ->select(
                'ep.*', 
                'u.name as author_name', 
                'u.email as author_email',
                'er.id as registration_id',
                'er.presentation_status',
                'er.presentation_queue',
                'er.presentation_time',
                'er.presentation_link',
                'er.presentation_location',
                'er.average_score as saved_average_score',
                'er.rejection_reason',
                'ep.product_category as selected_category',
                'ep.product_theme as selected_theme'
            )
            ->first();

        if (!$paper) {
            abort(404, 'Paper not found');
        }

        // Get all rubric categories and items for this event
        $categories = DB::table('rubric_categories')
            ->where('event_id', $event->id)
            ->orderBy('order')
            ->get();

        $rubricItems = [];
        foreach ($categories as $category) {
            $items = DB::table('rubric_items')
                ->where('rubric_category_id', $category->id)
                ->orderBy('order')
                ->get();
            $rubricItems[$category->id] = $items;
        }

        // Get all jury evaluations for this paper
        $juryScores = DB::table('rubric_item_scores as ris')
            ->join('users as u', 'ris.evaluator_id', '=', 'u.id')
            ->join('rubric_items as ri', 'ris.rubric_item_id', '=', 'ri.id')
            ->join('rubric_categories as rc', 'ri.rubric_category_id', '=', 'rc.id')
            ->where('ris.event_paper_id', $paperId)
            ->select(
                'ris.*',
                'u.name as evaluator_name',
                'u.email as evaluator_email',
                'ri.name as item_name',
                'ri.max_score as item_max_score',
                'rc.name as category_name',
                'rc.id as category_id',
                'ri.id as item_id'
            )
            ->get();

        // Group scores by evaluator
        $evaluatorScores = [];
        foreach ($juryScores as $score) {
            if (!isset($evaluatorScores[$score->evaluator_id])) {
                $evaluatorScores[$score->evaluator_id] = [
                    'name' => $score->evaluator_name,
                    'email' => $score->evaluator_email,
                    'scores' => [],
                    'total_score' => 0,
                    'category_totals' => []
                ];
            }

            $evaluatorScores[$score->evaluator_id]['scores'][$score->item_id] = $score;
            $evaluatorScores[$score->evaluator_id]['total_score'] += $score->score;

            if (!isset($evaluatorScores[$score->evaluator_id]['category_totals'][$score->category_id])) {
                $evaluatorScores[$score->evaluator_id]['category_totals'][$score->category_id] = 0;
            }
            $evaluatorScores[$score->evaluator_id]['category_totals'][$score->category_id] += $score->score;
        }

        // Calculate average score from all evaluators
        $calculatedAverageScore = count($evaluatorScores) > 0 
            ? collect($evaluatorScores)->avg('total_score') 
            : 0;

        // Get awards for Innovation events
        $awards = collect();
        $participantAwards = collect();
        if (!$event->delivery_mode) { // Innovation event
            $awards = EventAward::where('event_id', $event->id)
                ->orderBy('rank')
                ->get();
            
            $participantAwards = ParticipantAward::where('event_id', $event->id)
                ->where('user_id', $paper->user_id)
                ->with('award')
                ->get();
        }

        return view('organizer.evaluation-results.paper-details', compact(
            'event',
            'paper',
            'categories',
            'rubricItems',
            'evaluatorScores',
            'calculatedAverageScore',
            'eventType',
            'awards',
            'participantAwards'
        ));
    }

    /**
     * Export evaluation results to CSV
     */
    public function export(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Implementation for CSV export
        // To be implemented based on requirements

        return back()->with('info', 'Export feature coming soon!');
    }

    /**
     * Approve participant for presentation
     */
    public function approvePresentation(Request $request, Event $event, $paperId)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'presentation_queue' => 'nullable|integer|min:1',
            'presentation_time' => 'nullable|date',
            'presentation_link' => 'nullable|url',
            'presentation_location' => 'nullable|string|max:255',
        ]);

        // Get paper and participant info
        $paper = DB::table('event_papers')
            ->where('id', $paperId)
            ->where('event_id', $event->id)
            ->first();

        if (!$paper) {
            return back()->with('error', 'Paper not found');
        }

        // Get registration
        $registration = DB::table('event_registrations')
            ->where('user_id', $paper->user_id)
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Registration not found for this participant');
        }

        // Calculate average score from jury
        $averageScore = DB::table('rubric_item_scores')
            ->where('event_paper_id', $paperId)
            ->avg('score');

        // Update registration with approval
        DB::table('event_registrations')
            ->where('id', $registration->id)
            ->update([
                'presentation_status' => 'selected',
                'presentation_queue' => $request->presentation_queue,
                'presentation_time' => $request->presentation_time,
                'presentation_link' => $request->presentation_link,
                'presentation_location' => $request->presentation_location,
                'average_score' => $averageScore,
                'presentation_notified_at' => now(),
                'updated_at' => now()
            ]);

        // Get user to send notification
        $user = \App\Models\User::find($paper->user_id);
        
        // Get updated registration as Eloquent model for notification
        $updatedRegistration = \App\Models\EventRegistration::find($registration->id);
        
        // Send notification
        $user->notify(new \App\Notifications\PresentationSelectedNotification(
            $event,
            $updatedRegistration,
            $averageScore,
            $request->presentation_queue,
            $request->presentation_time,
            $request->presentation_link,
            $request->presentation_location
        ));

        return back()->with('success', 'Participant approved for presentation! Notification sent.');
    }

    /**
     * Reject participant for presentation
     */
    public function rejectPresentation(Request $request, Event $event, $paperId)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Get paper and participant info
        $paper = DB::table('event_papers')
            ->where('id', $paperId)
            ->where('event_id', $event->id)
            ->first();

        if (!$paper) {
            return back()->with('error', 'Paper not found');
        }

        // Get registration
        $registration = DB::table('event_registrations')
            ->where('user_id', $paper->user_id)
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Registration not found for this participant');
        }

        // Calculate average score from jury
        $averageScore = DB::table('rubric_item_scores')
            ->where('event_paper_id', $paperId)
            ->avg('score');

        // Update registration with rejection
        DB::table('event_registrations')
            ->where('id', $registration->id)
            ->update([
                'presentation_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'average_score' => $averageScore,
                'presentation_notified_at' => now(),
                'updated_at' => now()
            ]);

        // Get user to send notification
        $user = \App\Models\User::find($paper->user_id);
        
        // Send notification
        $user->notify(new \App\Notifications\PresentationRejectedNotification(
            $event,
            $averageScore,
            $request->rejection_reason
        ));

        return back()->with('success', 'Participant notified of rejection.');
    }
}
