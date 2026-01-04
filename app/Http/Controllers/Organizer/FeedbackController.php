<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display a listing of events with feedback (innovation or conference)
     */
    public function index(Request $request)
    {
        $organizer = Auth::user();
        $type = $request->input('type', 'innovation'); // Default to innovation
        
        // Build query based on event type
        $query = Event::where('organizer_id', $organizer->id)
            ->whereHas('registrations.feedback')
            ->withCount(['registrations as feedback_count' => function($query) {
                $query->whereHas('feedback');
            }])
            ->with('category');
        
        // Filter by event type based on category
        if ($type === 'conference') {
            // Conference events: Academic Conference category
            $query->whereHas('category', function($q) {
                $q->where('name', 'Academic Conference');
            });
        } else {
            // Innovation events: Innovation Competition category
            $query->whereHas('category', function($q) {
                $q->where('name', 'Innovation Competition');
            });
        }
        
        $events = $query->orderBy('start_date', 'desc')->get();
        
        return view('organizer.feedback.index', compact('events', 'type'));
    }
    
    /**
     * Display feedback for a specific event
     */
    public function show(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this event');
        }
        
        // Verify it's an innovation event
        if (!$event->category || $event->category->name !== 'Innovation Competition') {
            return redirect()->route('organizer.feedback.index')
                ->with('error', 'Feedback is only available for Innovation Competition events');
        }
        
        // Get all feedback with registrations and users
        $allFeedback = Feedback::whereHas('eventRegistration', function($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->with(['eventRegistration.user'])
            ->get();
        
        // Separate participant and jury feedback
        $participantFeedback = $allFeedback->filter(function($feedback) {
            return !$feedback->eventRegistration->juryMappingsAsReviewer()->exists();
        });
        
        $juryFeedback = $allFeedback->filter(function($feedback) {
            return $feedback->eventRegistration->juryMappingsAsReviewer()->exists();
        });
        
        // Calculate averages for participants
        $participantAverages = [
            'overall_rating' => round($participantFeedback->avg('overall_rating'), 1),
            'content_rating' => round($participantFeedback->avg('content_rating'), 1),
            'organization_rating' => round($participantFeedback->avg('organization_rating'), 1),
            'platform_rating' => round($participantFeedback->avg('platform_rating'), 1),
            'venue_rating' => round($participantFeedback->avg('venue_rating'), 1),
            'would_recommend_percentage' => $participantFeedback->count() > 0 
                ? round(($participantFeedback->where('would_recommend', 1)->count() / $participantFeedback->count()) * 100) 
                : 0,
            'count' => $participantFeedback->count()
        ];
        
        // Calculate averages for jury
        $juryAverages = [
            'overall_rating' => round($juryFeedback->avg('overall_rating'), 1),
            'content_rating' => round($juryFeedback->avg('content_rating'), 1),
            'organization_rating' => round($juryFeedback->avg('organization_rating'), 1),
            'platform_rating' => round($juryFeedback->avg('platform_rating'), 1),
            'venue_rating' => round($juryFeedback->avg('venue_rating'), 1),
            'would_recommend_percentage' => $juryFeedback->count() > 0 
                ? round(($juryFeedback->where('would_recommend', 1)->count() / $juryFeedback->count()) * 100) 
                : 0,
            'count' => $juryFeedback->count()
        ];
        
        return view('organizer.feedback.show', compact(
            'event',
            'participantFeedback',
            'juryFeedback',
            'participantAverages',
            'juryAverages'
        ));
    }
    
    /**
     * Export feedback data to CSV
     */
    public function export(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this event');
        }
        
        $feedbacks = Feedback::whereHas('eventRegistration', function($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->with(['eventRegistration.user'])
            ->get();
        
        $filename = 'feedback_' . str_replace(' ', '_', $event->title) . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($feedbacks) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Role',
                'Overall Rating',
                'Content Rating',
                'Organization Rating',
                'Platform Rating',
                'Venue Rating',
                'Would Recommend',
                'Comments',
                'Suggestions',
                'System Feedback',
                'Submitted At'
            ]);
            
            // Data rows
            foreach ($feedbacks as $feedback) {
                $isJury = $feedback->eventRegistration->juryMappingsAsReviewer()->exists();
                
                fputcsv($file, [
                    $feedback->id,
                    $feedback->eventRegistration->user->name,
                    $feedback->eventRegistration->user->email,
                    $isJury ? 'Jury' : 'Participant',
                    $feedback->overall_rating ?? 'N/A',
                    $feedback->content_rating ?? 'N/A',
                    $feedback->organization_rating ?? 'N/A',
                    $feedback->platform_rating ?? 'N/A',
                    $feedback->venue_rating ?? 'N/A',
                    $feedback->would_recommend ? 'Yes' : 'No',
                    $feedback->comments ?? '',
                    $feedback->suggestions ?? '',
                    $feedback->system_feedback ?? '',
                    $feedback->submitted_at ? $feedback->submitted_at->format('Y-m-d H:i:s') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
