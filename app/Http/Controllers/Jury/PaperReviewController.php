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

        return view('jury.papers.review', compact('assignment', 'review'));
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

        $request->validate([
            'originality_score' => 'required|numeric|min:1|max:10',
            'methodology_score' => 'required|numeric|min:1|max:10',
            'clarity_score' => 'required|numeric|min:1|max:10',
            'contribution_score' => 'required|numeric|min:1|max:10',
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'comments' => 'nullable|string',
            'confidential_comments' => 'nullable|string',
            'recommendation' => 'required|in:accept,minor_revision,major_revision,reject',
            'save_as' => 'required|in:draft,submit',
        ]);

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
