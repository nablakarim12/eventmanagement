<?php

namespace App\Http\Controllers\Jury;

use App\Http\Controllers\Controller;
use App\Models\JuryAssignment;
use App\Models\PaperReview;
use App\Models\PaperSubmission;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaperReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all assigned papers for the jury member
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get jury registrations
        $juryRegistrations = EventRegistration::where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->whereIn('role', ['jury', 'both'])
            ->whereNotNull('checked_in_at')
            ->pluck('id');

        $assignments = JuryAssignment::whereIn('jury_registration_id', $juryRegistrations)
            ->with(['paperSubmission.event', 'paperSubmission.authors', 'review'])
            ->latest()
            ->get();

        return view('jury.papers.index', compact('assignments'));
    }

    /**
     * Show a specific paper for review
     */
    public function show(JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        // Verify this assignment belongs to the current user
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        $assignment->load([
            'paperSubmission.event',
            'paperSubmission.authors',
            'review'
        ]);

        return view('jury.papers.show', compact('assignment'));
    }

    /**
     * Download paper PDF
     */
    public function download(JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        $paper = $assignment->paperSubmission;

        if (!Storage::disk('public')->exists($paper->paper_file_path)) {
            return back()->with('error', 'Paper file not found.');
        }

        return Storage::disk('public')->download($paper->paper_file_path, $paper->paper_file_name);
    }

    /**
     * Show review form
     */
    public function createReview(JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        // Check if review already exists
        $review = $assignment->review;
        
        if ($review && $review->status === 'submitted') {
            return redirect()->route('jury.papers.show', $assignment)
                ->with('info', 'You have already submitted a review for this paper.');
        }

        $assignment->load('paperSubmission.event', 'paperSubmission.authors');
        
        // Get active review criteria for this event
        $criteria = $assignment->paperSubmission->event->reviewCriteria()->active()->get();
        
        // If no criteria set, use default ones
        if ($criteria->isEmpty()) {
            $criteria = collect([
                (object)['id' => null, 'name' => 'originality', 'description' => 'Novelty and innovation', 'max_score' => 10, 'weight' => 1],
                (object)['id' => null, 'name' => 'methodology', 'description' => 'Soundness of methods', 'max_score' => 10, 'weight' => 1],
                (object)['id' => null, 'name' => 'clarity', 'description' => 'Quality of writing', 'max_score' => 10, 'weight' => 1],
                (object)['id' => null, 'name' => 'contribution', 'description' => 'Significance to field', 'max_score' => 10, 'weight' => 1],
            ]);
        }

        return view('jury.papers.review', compact('assignment', 'review', 'criteria'));
    }

    /**
     * Store or update review
     */
    public function storeReview(Request $request, JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        // Get event criteria for dynamic validation
        $criteria = $assignment->paperSubmission->event->reviewCriteria()->active()->get();
        
        // Build validation rules dynamically
        $validationRules = [
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'comments' => 'nullable|string',
            'confidential_comments' => 'nullable|string',
            'recommendation' => 'required|in:accept,minor_revision,major_revision,reject',
            'save_as' => 'required|in:draft,submit',
        ];
        
        // If custom criteria exist, validate them
        if ($criteria->isNotEmpty()) {
            foreach ($criteria as $criterion) {
                $fieldName = 'score_' . $criterion->id;
                $validationRules[$fieldName] = 'required|numeric|min:0|max:' . $criterion->max_score;
            }
        } else {
            // Fallback to default criteria
            $validationRules = array_merge($validationRules, [
                'originality_score' => 'required|numeric|min:1|max:10',
                'methodology_score' => 'required|numeric|min:1|max:10',
                'clarity_score' => 'required|numeric|min:1|max:10',
                'contribution_score' => 'required|numeric|min:1|max:10',
            ]);
        }

        $request->validate($validationRules);

        // Calculate overall score from criteria
        $overallScore = 0;
        $totalWeight = 0;
        
        if ($criteria->isNotEmpty()) {
            foreach ($criteria as $criterion) {
                $fieldName = 'score_' . $criterion->id;
                $score = $request->input($fieldName);
                $overallScore += $score * $criterion->weight;
                $totalWeight += $criterion->weight;
            }
            $overallScore = $totalWeight > 0 ? $overallScore / $totalWeight : 0;
            
            // Store scores in JSON format for custom criteria
            $scoresData = [];
            foreach ($criteria as $criterion) {
                $fieldName = 'score_' . $criterion->id;
                $scoresData[$criterion->id] = $request->input($fieldName);
            }
            
            $reviewData = [
                'paper_submission_id' => $assignment->paper_submission_id,
                'jury_assignment_id' => $assignment->id,
                'jury_registration_id' => $assignment->jury_registration_id,
                'custom_scores' => json_encode($scoresData),
                'overall_score' => $overallScore,
                'strengths' => $request->strengths,
                'weaknesses' => $request->weaknesses,
                'comments' => $request->comments,
                'confidential_comments' => $request->confidential_comments,
                'recommendation' => $request->recommendation,
            ];
        } else {
            // Use default fields
            $reviewData = [
                'paper_submission_id' => $assignment->paper_submission_id,
                'jury_assignment_id' => $assignment->id,
                'jury_registration_id' => $assignment->jury_registration_id,
                'originality_score' => $request->originality_score,
                'methodology_score' => $request->methodology_score,
                'clarity_score' => $request->clarity_score,
                'contribution_score' => $request->contribution_score,
                'strengths' => $request->strengths,
                'weaknesses' => $request->weaknesses,
                'comments' => $request->comments,
                'confidential_comments' => $request->confidential_comments,
                'recommendation' => $request->recommendation,
            ];
        }

        $review = PaperReview::updateOrCreate(
            ['jury_assignment_id' => $assignment->id],
            $reviewData
        );

        if ($request->save_as === 'submit') {
            $review->submit();
            $message = 'Review submitted successfully!';
        } else {
            $message = 'Review saved as draft.';
        }

        return redirect()->route('jury.papers.index')
            ->with('success', $message);
    }

    /**
     * Accept assignment
     */
    public function acceptAssignment(JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        $assignment->accept();

        return back()->with('success', 'Assignment accepted.');
    }

    /**
     * Decline assignment
     */
    public function declineAssignment(Request $request, JuryAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->juryRegistration->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);

        $assignment->decline($request->decline_reason);

        return redirect()->route('jury.papers.index')
            ->with('success', 'Assignment declined.');
    }
}
