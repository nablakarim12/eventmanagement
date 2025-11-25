<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\PaperSubmission;
use App\Models\PaperAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show form to submit a paper for an event
     */
    public function create(Event $event)
    {
        $user = Auth::user();
        
        // Check if user has a confirmed registration for this event
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$registration) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be registered and approved for this event to submit a paper.');
        }

        // Check if already submitted
        $existingSubmission = PaperSubmission::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return redirect()->route('papers.show', $existingSubmission)
                ->with('info', 'You have already submitted a paper for this event.');
        }

        return view('papers.create', compact('event', 'registration'));
    }

    /**
     * Store a new paper submission
     */
    public function store(Request $request, Event $event)
    {
        $user = Auth::user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|max:2000',
            'keywords' => 'nullable|string|max:500',
            'paper_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.email' => 'required|email|max:255',
            'authors.*.affiliation' => 'nullable|string|max:255',
            'authors.*.country' => 'nullable|string|max:100',
            'authors.*.is_corresponding' => 'nullable|boolean',
        ]);

        // Check registration
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Invalid registration.');
        }

        // Upload paper file
        $file = $request->file('paper_file');
        $filename = Str::slug($request->title) . '_' . time() . '.pdf';
        $path = $file->storeAs('papers', $filename, 'public');

        // Create paper submission
        $submission = PaperSubmission::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'registration_id' => $registration->id,
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'paper_file_path' => $path,
            'paper_file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'submitted',
        ]);

        // Create authors
        foreach ($request->authors as $index => $authorData) {
            PaperAuthor::create([
                'paper_submission_id' => $submission->id,
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'affiliation' => $authorData['affiliation'] ?? null,
                'country' => $authorData['country'] ?? null,
                'is_corresponding' => $authorData['is_corresponding'] ?? false,
                'order' => $index + 1,
            ]);
        }

        return redirect()->route('papers.show', $submission)
            ->with('success', 'Paper submitted successfully!');
    }

    /**
     * Show a paper submission
     */
    public function show(PaperSubmission $paper)
    {
        $user = Auth::user();
        
        // Only paper submitter can view
        if ($paper->user_id !== $user->id) {
            abort(403);
        }

        $paper->load(['authors', 'reviews.juryRegistration.user', 'event']);

        return view('papers.show', compact('paper'));
    }

    /**
     * Download the paper PDF
     */
    public function download(PaperSubmission $paper)
    {
        $user = Auth::user();
        
        // Only paper submitter can download
        if ($paper->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($paper->paper_file_path)) {
            return back()->with('error', 'Paper file not found.');
        }

        return Storage::disk('public')->download($paper->paper_file_path, $paper->paper_file_name);
    }

    /**
     * List all papers submitted by the user
     */
    public function index()
    {
        $user = Auth::user();
        
        $papers = PaperSubmission::where('user_id', $user->id)
            ->with(['event', 'authors'])
            ->latest()
            ->get();

        return view('papers.index', compact('papers'));
    }
}
