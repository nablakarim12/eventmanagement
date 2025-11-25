<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show registration form for an event
     */
    public function create(Request $request, Event $event)
    {
        // Check if event is available for registration using the new helper method
        if (!$event->isRegistrationOpen()) {
            $status = $event->getRegistrationStatus();
            
            $errorMessage = match($status) {
                'event_ended' => 'This event has already ended.',
                'deadline_passed' => 'Registration deadline has passed.',
                'full' => 'This event is currently full.',
                'not_available' => 'Registration is not available for this event.',
                default => 'Registration is not available for this event.'
            };
            
            return redirect()->route('events.show', $event->slug)
                ->with('error', $errorMessage);
        }

        // Check if user is already registered
        if (Auth::user()->isRegisteredFor($event)) {
            return redirect()->route('events.show', $event->slug)
                ->with('info', 'You are already registered for this event.');
        }

        // Get role from URL parameter (for pre-filling the form)
        $preselectedRole = $request->query('role');
        if ($preselectedRole && !in_array($preselectedRole, ['participant', 'jury', 'both'])) {
            $preselectedRole = null;
        }

        return view('registration.create', compact('event', 'preselectedRole'));
    }

    /**
     * Store a new event registration
     */
    public function store(Request $request, Event $event)
    {
        // Base validation rules
        $rules = [
            'role' => 'required|in:participant,jury,both',
            'special_requirements' => 'nullable|string|max:500',
            'dietary_restrictions' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'terms_accepted' => 'required|accepted',
        ];

        // Add jury qualification validation if role is jury or both
        if (in_array($request->role, ['jury', 'both'])) {
            $rules['jury_qualification_summary'] = 'required|string|max:1000';
            $rules['jury_institution'] = 'required|string|max:255';
            $rules['jury_position'] = 'required|string|max:255';
            $rules['jury_years_experience'] = 'required|integer|min:0|max:99';
            $rules['jury_expertise_areas'] = 'required|string|max:500';
            $rules['jury_experience'] = 'required|string|max:2000';
            $rules['jury_qualification_documents'] = 'required|array|max:5';
            $rules['jury_qualification_documents.*'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120';
        }

        // Validate registration data
        $validated = $request->validate($rules);

        // Check if event is still available for registration
        if (!$this->canRegisterForEvent($event)) {
            throw ValidationException::withMessages([
                'event' => 'Registration is no longer available for this event.'
            ]);
        }

        // Check if user is already registered
        if (Auth::user()->isRegisteredFor($event)) {
            throw ValidationException::withMessages([
                'registration' => 'You are already registered for this event.'
            ]);
        }

        // Create registration within transaction
        DB::transaction(function () use ($event, $validated, $request) {
            // Prepare registration data
            $registrationData = [
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'role' => $validated['role'],
                'amount_paid' => $event->registration_fee ?? 0,
                'payment_status' => $event->is_free ? 'completed' : 'pending',
                'status' => $event->requires_approval ? 'pending' : 'confirmed',
                'special_requirements' => $validated['special_requirements'] ?? null,
                'dietary_restrictions' => $validated['dietary_restrictions'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            ];

            // Add jury qualification data if applicable
            if (in_array($validated['role'], ['jury', 'both'])) {
                $registrationData['jury_qualification_summary'] = $validated['jury_qualification_summary'];
                $registrationData['jury_institution'] = $validated['jury_institution'];
                $registrationData['jury_position'] = $validated['jury_position'];
                $registrationData['jury_years_experience'] = $validated['jury_years_experience'];
                $registrationData['jury_expertise_areas'] = $validated['jury_expertise_areas'];
                $registrationData['jury_experience'] = $validated['jury_experience'];
                $registrationData['approval_status'] = 'pending'; // Jury applications need approval
                
                // Handle document uploads
                if ($request->hasFile('jury_qualification_documents')) {
                    $documentPaths = [];
                    foreach ($request->file('jury_qualification_documents') as $file) {
                        $path = $file->store('certificates', 'public');
                        $documentPaths[] = $path;
                    }
                    $registrationData['jury_qualification_documents'] = $documentPaths;
                }
            }

            // Create registration
            $registration = EventRegistration::create($registrationData);

            // Update event participant count
            $event->increment('current_participants');

            // If confirmed and free, mark as confirmed (only for participants)
            if ($event->is_free && !$event->requires_approval && $validated['role'] === 'participant') {
                $registration->confirm();
            }
        });

        $message = $validated['role'] === 'participant' 
            ? 'Successfully registered for ' . $event->title . '!'
            : 'Registration submitted! Your jury application will be reviewed by the organizer.';

        return redirect()->route('dashboard.registrations')
            ->with('success', $message);
    }

    /**
     * Show user's registrations
     */
    public function index()
    {
        $registrations = Auth::user()->eventRegistrations()
            ->with(['event', 'event.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.registrations.index', compact('registrations'));
    }

    /**
     * Show specific registration details
     */
    public function show(EventRegistration $registration)
    {
        // Ensure user can only view their own registrations
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        $registration->load(['event', 'event.category', 'event.organizer']);

        return view('dashboard.registrations.show', compact('registration'));
    }

    /**
     * Cancel a registration
     */
    public function cancel(EventRegistration $registration)
    {
        // Ensure user can only cancel their own registrations
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if cancellation is allowed
        if ($registration->isCancelled()) {
            return redirect()->back()->with('error', 'Registration is already cancelled.');
        }

        // Check cancellation deadline (e.g., 24 hours before event)
        if ($registration->event->start_date->subDay()->isPast()) {
            return redirect()->back()->with('error', 'Cancellation deadline has passed.');
        }

        DB::transaction(function () use ($registration) {
            // Cancel registration
            $registration->cancel();

            // Decrease event participant count
            $registration->event->decrement('current_participants');
        });

        return redirect()->back()->with('success', 'Registration cancelled successfully.');
    }

    /**
     * Check if user can register for the event
     */
    private function canRegisterForEvent(Event $event): bool
    {
        // Check if event is published and public
        if ($event->status !== 'published' || !$event->is_public) {
            return false;
        }

        // Check if event has already started
        if ($event->start_date->isPast()) {
            return false;
        }

        // Check registration deadline
        if ($event->registration_deadline && $event->registration_deadline->isPast()) {
            return false;
        }

        // Check capacity
        if ($event->max_participants && $event->current_participants >= $event->max_participants) {
            return false;
        }

        return true;
    }
}
