<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->middleware('auth:organizer');
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = Event::where('organizer_id', $organizer->id)
            ->with('category');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->latest()->paginate(10);
        $categories = EventCategory::all();

        return view('organizer.events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = EventCategory::all();
        
        // Check if a specific event type is requested
        $eventType = $request->query('type');
        
        if ($eventType === 'innovation') {
            return view('organizer.events.create_innovation', compact('categories'));
        } elseif ($eventType === 'academic' || $eventType === 'conference') {
            return view('organizer.events.create_conference', compact('categories'));
        }
        
        // Default: show selection page
        return view('organizer.events.select_type');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $organizer = Auth::guard('organizer')->user();

            // Check if this is an innovation competition form
            $isInnovationForm = $request->input('event_form_type') === 'innovation';
            
            // Check if this is a conference form
            $isConferenceForm = $request->input('event_form_type') === 'conference';

            // Check if this is an academic conference by category
            $category = EventCategory::find($request->category_id);
            $isConference = $category && (
                $category->name === 'Academic Conference' || 
                strpos($category->name, 'Conference') !== false
            );

            // Base validation rules
            $validationRules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:event_categories,id',
                'venue_name' => 'required|string|max:255',
                'venue_address' => 'required|string',
                'city' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'max_participants' => 'required|integer|min:1',
                'registration_fee' => 'required|numeric|min:0',
                'featured_image' => $isConferenceForm || $isInnovationForm ? 'required|image|mimes:jpeg,png,jpg,gif|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            // Add conference-specific validation
            if ($isConferenceForm) {
                $validationRules['delivery_mode'] = 'required|in:face_to_face,online,hybrid';
                $validationRules['conference_categories'] = 'required|array|min:1';
                $validationRules['conference_categories.*'] = 'required|string|max:100';
                
                // Delivery mode specific validation
                if (in_array($request->delivery_mode, ['face_to_face', 'hybrid'])) {
                    $validationRules['f2f_start_date'] = 'required|date';
                    $validationRules['f2f_end_date'] = 'required|date|after_or_equal:f2f_start_date';
                    $validationRules['f2f_start_time'] = 'required';
                    $validationRules['f2f_end_time'] = 'required';
                    $validationRules['f2f_reviewer_registration_deadline'] = 'nullable|date';
                    $validationRules['f2f_paper_submission_deadline'] = 'nullable|date';
                    $validationRules['f2f_review_deadline'] = 'nullable|date';
                    $validationRules['f2f_acceptance_notification_date'] = 'nullable|date';
                }
                
                if (in_array($request->delivery_mode, ['online', 'hybrid'])) {
                    $validationRules['online_platform_url'] = 'nullable|url';
                    $validationRules['online_start_date'] = 'required|date';
                    $validationRules['online_end_date'] = 'required|date|after_or_equal:online_start_date';
                    $validationRules['online_start_time'] = 'required';
                    $validationRules['online_end_time'] = 'required';
                    $validationRules['online_reviewer_registration_deadline'] = 'nullable|date';
                    $validationRules['online_paper_submission_deadline'] = 'nullable|date';
                    $validationRules['online_review_deadline'] = 'nullable|date';
                    $validationRules['online_acceptance_notification_date'] = 'nullable|date';
                }
            }

            // Add delivery mode validation for innovation competitions
            if ($isInnovationForm) {
                $validationRules['delivery_mode'] = 'required|in:face_to_face,online,hybrid';
                $validationRules['innovation_categories'] = 'required|array|min:1';
                $validationRules['innovation_categories.*'] = 'required|string|max:100';
                
                // Validate F2F fields if delivery mode includes F2F
                if (in_array($request->delivery_mode, ['face_to_face', 'hybrid'])) {
                    $validationRules['f2f_start_date'] = 'required|date';
                    $validationRules['f2f_end_date'] = 'required|date|after_or_equal:f2f_start_date';
                    $validationRules['f2f_start_time'] = 'required';
                    $validationRules['f2f_end_time'] = 'required';
                }
                
                // Validate Online fields if delivery mode includes Online
                if (in_array($request->delivery_mode, ['online', 'hybrid'])) {
                    $validationRules['online_start_date'] = 'required|date';
                    $validationRules['online_end_date'] = 'required|date|after_or_equal:online_start_date';
                    $validationRules['online_start_time'] = 'required';
                    $validationRules['online_end_time'] = 'required';
                }
            }
            
            // Standard validation for truly standard events (not innovation or conference)
            if (!$isInnovationForm && !$isConferenceForm) {
                $validationRules['registration_deadline'] = 'required|date';
                $validationRules['start_date'] = 'required|date';
                $validationRules['end_date'] = 'required|date|after_or_equal:start_date';
                $validationRules['start_time'] = 'required';
                $validationRules['end_time'] = 'required';
            }

            // Log validation rules for debugging
            logger()->info('Conference Form Validation Rules:', $validationRules);
            logger()->info('Request Data:', $request->all());

            try {
                $request->validate($validationRules);
            } catch (\Illuminate\Validation\ValidationException $e) {
                logger()->error('Validation failed:', $e->errors());
                throw $e;
            }

            // Create event with minimal data
            $registrationFee = (float)($request->registration_fee ?? 0);
            
            $eventData = [
                'organizer_id' => $organizer->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'venue_name' => $request->venue_name,
                'venue_address' => $request->venue_address,
                'city' => $request->city,
                'country' => $request->country,
                'slug' => Str::slug($request->title . '-' . time()),
                'status' => $request->status ?? 'draft',
                'registration_fee' => $registrationFee,
                'max_participants' => $request->max_participants ?? 100,
            ];

            // Add registration_deadline only for standard events
            if (!$isInnovationForm && !$isConferenceForm) {
                $eventData['registration_deadline'] = $request->registration_deadline;
            }

            // Add conference-specific data
            if ($isConferenceForm) {
                $eventData['delivery_mode'] = $request->delivery_mode;
                $eventData['conference_categories'] = array_values(array_filter($request->conference_categories));
                
                // Add F2F specific fields
                if (in_array($request->delivery_mode, ['face_to_face', 'hybrid'])) {
                    // Parse datetime-local inputs (format: 2025-12-18T21:00)
                    $f2fStartDateTime = str_replace('T', ' ', $request->f2f_start_date);
                    $f2fEndDateTime = str_replace('T', ' ', $request->f2f_end_date);
                    
                    $eventData['f2f_start_date'] = $f2fStartDateTime;
                    $eventData['f2f_end_date'] = $f2fEndDateTime;
                    $eventData['f2f_start_time'] = $request->f2f_start_time;
                    $eventData['f2f_end_time'] = $request->f2f_end_time;
                    $eventData['f2f_reviewer_registration_deadline'] = $request->f2f_reviewer_registration_deadline ? str_replace('T', ' ', $request->f2f_reviewer_registration_deadline) : null;
                    $eventData['f2f_paper_submission_deadline'] = $request->f2f_paper_submission_deadline ? str_replace('T', ' ', $request->f2f_paper_submission_deadline) : null;
                    $eventData['f2f_review_deadline'] = $request->f2f_review_deadline ? str_replace('T', ' ', $request->f2f_review_deadline) : null;
                    $eventData['f2f_acceptance_notification_date'] = $request->f2f_acceptance_notification_date ? str_replace('T', ' ', $request->f2f_acceptance_notification_date) : null;
                    $eventData['f2f_payment_deadline'] = $request->f2f_payment_deadline ? str_replace('T', ' ', $request->f2f_payment_deadline) : null;
                    
                    // Also populate legacy columns for compatibility
                    $eventData['start_date'] = $f2fStartDateTime;
                    $eventData['end_date'] = $f2fEndDateTime;
                    $eventData['start_time'] = $request->f2f_start_time;
                    $eventData['end_time'] = $request->f2f_end_time;
                }
                
                // Add Online specific fields
                if (in_array($request->delivery_mode, ['online', 'hybrid'])) {
                    // Parse datetime-local inputs
                    $onlineStartDateTime = str_replace('T', ' ', $request->online_start_date);
                    $onlineEndDateTime = str_replace('T', ' ', $request->online_end_date);
                    
                    $eventData['online_start_date'] = $onlineStartDateTime;
                    $eventData['online_end_date'] = $onlineEndDateTime;
                    $eventData['online_start_time'] = $request->online_start_time;
                    $eventData['online_end_time'] = $request->online_end_time;
                    $eventData['online_platform_url'] = $request->online_platform_url;
                    $eventData['online_reviewer_registration_deadline'] = $request->online_reviewer_registration_deadline ? str_replace('T', ' ', $request->online_reviewer_registration_deadline) : null;
                    $eventData['online_paper_submission_deadline'] = $request->online_paper_submission_deadline ? str_replace('T', ' ', $request->online_paper_submission_deadline) : null;
                    $eventData['online_review_deadline'] = $request->online_review_deadline ? str_replace('T', ' ', $request->online_review_deadline) : null;
                    $eventData['online_acceptance_notification_date'] = $request->online_acceptance_notification_date ? str_replace('T', ' ', $request->online_acceptance_notification_date) : null;
                    $eventData['online_payment_deadline'] = $request->online_payment_deadline ? str_replace('T', ' ', $request->online_payment_deadline) : null;
                    
                    // If no F2F, use online for legacy columns
                    if (!in_array($request->delivery_mode, ['face_to_face'])) {
                        $eventData['start_date'] = $onlineStartDateTime;
                        $eventData['end_date'] = $onlineEndDateTime;
                        $eventData['start_time'] = $request->online_start_time;
                        $eventData['end_time'] = $request->online_end_time;
                    }
                }
            } elseif ($isConference && !$isInnovationForm) {
                // Legacy conference handling (non-form)
                $eventData['paper_submission_deadline'] = $request->paper_submission_deadline;
                $eventData['min_abstract_words'] = $request->min_abstract_words ?? 200;
                $eventData['min_keywords'] = $request->min_keywords ?? 3;
                $eventData['max_paper_size_mb'] = $request->max_paper_size_mb ?? 10;
                $eventData['paper_format_guidelines'] = $request->paper_format_guidelines;
                $eventData['allow_multiple_submissions'] = $request->has('allow_multiple_submissions');
                $eventData['min_reviewers_per_paper'] = $request->min_reviewers_per_paper ?? 2;
                $eventData['review_deadline'] = $request->review_deadline;
            }

            // Add delivery mode specific data for innovation competitions
            if ($isInnovationForm) {
                $eventData['delivery_mode'] = $request->delivery_mode;
                
                // Save innovation categories
                $eventData['innovation_categories'] = array_values(array_filter($request->innovation_categories));
                
                // Save innovation themes
                if ($request->has('innovation_theme') && is_array($request->innovation_theme)) {
                    $eventData['innovation_theme'] = array_values(array_filter($request->innovation_theme));
                }
                
                // Add F2F specific fields
                if (in_array($request->delivery_mode, ['face_to_face', 'hybrid'])) {
                    // Parse datetime-local inputs (format: 2025-12-18T21:00)
                    $f2fStartDateTime = str_replace('T', ' ', $request->f2f_start_datetime);
                    $f2fEndDateTime = str_replace('T', ' ', $request->f2f_end_datetime);
                    
                    $eventData['f2f_start_date'] = $f2fStartDateTime;
                    $eventData['f2f_end_date'] = $f2fEndDateTime;
                    $eventData['f2f_start_time'] = $request->f2f_start_time;
                    $eventData['f2f_end_time'] = $request->f2f_end_time;
                    
                    // New innovation date fields
                    $eventData['f2f_registration_deadline'] = $request->f2f_registration_deadline ? str_replace('T', ' ', $request->f2f_registration_deadline) : null;
                    $eventData['f2f_jury_registration_deadline'] = $request->f2f_jury_registration_deadline ? str_replace('T', ' ', $request->f2f_jury_registration_deadline) : null;
                    $eventData['f2f_submission_deadline'] = $request->f2f_submission_deadline ? str_replace('T', ' ', $request->f2f_submission_deadline) : null;
                    $eventData['f2f_acceptance_notification_date'] = $request->f2f_acceptance_notification_date ? str_replace('T', ' ', $request->f2f_acceptance_notification_date) : null;
                    $eventData['f2f_payment_deadline'] = $request->f2f_payment_deadline_new ? str_replace('T', ' ', $request->f2f_payment_deadline_new) : null;
                    
                    // Extended deadlines (optional)
                    $eventData['f2f_extended_registration_deadline'] = $request->f2f_extended_registration_deadline ? str_replace('T', ' ', $request->f2f_extended_registration_deadline) : null;
                    $eventData['f2f_extended_jury_deadline'] = $request->f2f_extended_jury_deadline ? str_replace('T', ' ', $request->f2f_extended_jury_deadline) : null;
                    $eventData['f2f_extended_submission_deadline'] = $request->f2f_extended_submission_deadline ? str_replace('T', ' ', $request->f2f_extended_submission_deadline) : null;
                    $eventData['f2f_extended_notification_date'] = $request->f2f_extended_notification_date ? str_replace('T', ' ', $request->f2f_extended_notification_date) : null;
                    
                    // Also populate legacy columns for compatibility
                    $eventData['start_date'] = $f2fStartDateTime;
                    $eventData['end_date'] = $f2fEndDateTime;
                    $eventData['start_time'] = $request->f2f_start_time;
                    $eventData['end_time'] = $request->f2f_end_time;
                    
                    // Old extended deadlines handling removed - using new structure instead
                }
                
                // Add Online specific fields
                if (in_array($request->delivery_mode, ['online', 'hybrid'])) {
                    // Parse datetime-local inputs
                    $onlineStartDateTime = str_replace('T', ' ', $request->online_start_datetime);
                    $onlineEndDateTime = str_replace('T', ' ', $request->online_end_datetime);
                    
                    $eventData['online_start_date'] = $onlineStartDateTime;
                    $eventData['online_end_date'] = $onlineEndDateTime;
                    $eventData['online_start_time'] = $request->online_start_time;
                    $eventData['online_end_time'] = $request->online_end_time;
                    $eventData['online_platform_url'] = $request->online_platform_url;
                    
                    // New innovation date fields
                    $eventData['online_registration_deadline'] = $request->online_registration_deadline ? str_replace('T', ' ', $request->online_registration_deadline) : null;
                    $eventData['online_jury_registration_deadline_new'] = $request->online_jury_registration_deadline_new ? str_replace('T', ' ', $request->online_jury_registration_deadline_new) : null;
                    $eventData['online_submission_deadline'] = $request->online_submission_deadline ? str_replace('T', ' ', $request->online_submission_deadline) : null;
                    $eventData['online_acceptance_notification_date_new'] = $request->online_acceptance_notification_date_new ? str_replace('T', ' ', $request->online_acceptance_notification_date_new) : null;
                    $eventData['online_payment_deadline_new'] = $request->online_payment_deadline_new ? str_replace('T', ' ', $request->online_payment_deadline_new) : null;
                    
                    // Extended deadlines (optional)
                    $eventData['online_extended_registration_deadline'] = $request->online_extended_registration_deadline ? str_replace('T', ' ', $request->online_extended_registration_deadline) : null;
                    $eventData['online_extended_jury_deadline'] = $request->online_extended_jury_deadline ? str_replace('T', ' ', $request->online_extended_jury_deadline) : null;
                    $eventData['online_extended_submission_deadline'] = $request->online_extended_submission_deadline ? str_replace('T', ' ', $request->online_extended_submission_deadline) : null;
                    $eventData['online_extended_notification_date'] = $request->online_extended_notification_date ? str_replace('T', ' ', $request->online_extended_notification_date) : null;
                    
                    // If no F2F, use online for legacy columns
                    if (!in_array($request->delivery_mode, ['face_to_face'])) {
                        $eventData['start_date'] = $onlineStartDateTime;
                        $eventData['end_date'] = $onlineEndDateTime;
                        $eventData['start_time'] = $request->online_start_time;
                        $eventData['end_time'] = $request->online_end_time;
                    }
                    
                    // Old extended deadlines handling removed - using new structure instead
                }
            }
            
            // Standard event data (only for non-innovation AND non-conference forms)
            if (!$isInnovationForm && !$isConferenceForm) {
                $eventData['start_date'] = $request->start_date . ' ' . $request->start_time;
                $eventData['end_date'] = $request->end_date . ' ' . $request->end_time;
                $eventData['start_time'] = $request->start_time;
                $eventData['end_time'] = $request->end_time;
            }
            
            $event = new Event($eventData);
            
            // Set boolean values explicitly
            $event->is_free = $registrationFee == 0;
            $event->requires_approval = $request->has('requires_approval');
            $event->is_public = true;
            $event->allow_waitlist = false;
            
            // Save the event first to get an ID
            $event->save();

            // Handle featured image upload after event is saved (so we have an ID)
            if ($request->hasFile('featured_image')) {
                try {
                    $file = $request->file('featured_image');
                    
                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file: ' . $file->getErrorMessage());
                    }
                    
                    Log::info('Starting Cloudinary upload for new event ' . $event->id);
                    
                    // Upload to Cloudinary
                    $uploadResult = $this->cloudinaryService->uploadImage(
                        $file,
                        'events/posters',
                        [
                            'public_id' => 'event_' . $event->id . '_poster',
                            'overwrite' => true,
                            'transformation' => [
                                'width' => 1920,
                                'height' => 1080,
                                'crop' => 'limit',
                                'quality' => 'auto:good'
                            ]
                        ]
                    );
                    
                    // Save Cloudinary URL and public_id
                    $event->featured_image = $uploadResult['secure_url'];
                    $event->featured_image_public_id = $uploadResult['public_id'];
                    $event->save();
                    
                    Log::info('Image uploaded to Cloudinary: ' . $uploadResult['secure_url']);
                    
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload failed during event creation: ' . $e->getMessage());
                    // Don't fail the entire event creation for image upload issues
                }
            }

            return redirect()->route('organizer.events.index')
                ->with('success', 'Event created successfully!');

        } catch (\Exception $e) {
            Log::error('Event creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to create event. Please check all required fields.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // Check if the event belongs to the current organizer
        $organizer = Auth::guard('organizer')->user();
        if ($event->organizer_id !== $organizer->id) {
            abort(403, 'Unauthorized');
        }
        
        $event->load('category', 'organizer');
        
        return view('organizer.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Check if the event belongs to the current organizer
        $organizer = Auth::guard('organizer')->user();
        if ($event->organizer_id !== $organizer->id) {
            abort(403, 'Unauthorized');
        }
        
        $categories = EventCategory::all();
        
        // Check if this is an innovation competition event
        $innovationCategory = EventCategory::where('name', 'Innovation Competition')->first();
        if ($innovationCategory && $event->category_id === $innovationCategory->id) {
            return view('organizer.events.edit_innovation', compact('event', 'categories'));
        }
        
        // Check if this is a conference event (has delivery_mode)
        if ($event->delivery_mode && in_array($event->delivery_mode, ['face_to_face', 'online', 'hybrid'])) {
            return view('organizer.events.edit_conference', compact('event', 'categories'));
        }
        
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        try {
            // Check if the event belongs to the current organizer
            $organizer = Auth::guard('organizer')->user();
            if ($event->organizer_id !== $organizer->id) {
                abort(403, 'Unauthorized');
            }

            // Check if this is an innovation competition event
            $isInnovationEvent = $event->category && $event->category->name === 'Innovation Competition';
            
            // Check if this is a conference event (has delivery_mode BUT is not innovation)
            $isConferenceEvent = !$isInnovationEvent && $event->delivery_mode && in_array($event->delivery_mode, ['face_to_face', 'online', 'hybrid']);

            // Base validation rules
            $validationRules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:event_categories,id',
                'status' => 'required|in:draft,published,cancelled',
                'venue_name' => 'nullable|string|max:255',
                'venue_address' => 'nullable|string|max:500',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'registration_fee' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
            ];

            // Conditional validation for innovation/conference vs standard events
            if ($isInnovationEvent || $isConferenceEvent) {
                $validationRules['delivery_mode'] = 'required|in:face_to_face,online,hybrid';
                
                if ($isInnovationEvent) {
                    $validationRules['innovation_categories'] = 'required|array|min:1';
                }
                
                if ($isConferenceEvent) {
                    $validationRules['conference_categories'] = 'required|array|min:1';
                }
                
                // Validate based on delivery mode
                if ($request->delivery_mode === 'face_to_face' || $request->delivery_mode === 'hybrid') {
                    $validationRules['f2f_start_date'] = 'required|date';
                    $validationRules['f2f_end_date'] = 'required|date|after_or_equal:f2f_start_date';
                    $validationRules['f2f_start_time'] = 'required';
                    $validationRules['f2f_end_time'] = 'required';
                }
                
                if ($request->delivery_mode === 'online' || $request->delivery_mode === 'hybrid') {
                    $validationRules['online_start_date'] = 'required|date';
                    $validationRules['online_end_date'] = 'required|date|after_or_equal:online_start_date';
                    $validationRules['online_start_time'] = 'required';
                    $validationRules['online_end_time'] = 'required';
                }
            } else {
                $validationRules['start_date'] = 'required|date';
                $validationRules['end_date'] = 'required|date|after_or_equal:start_date';
                $validationRules['registration_deadline'] = 'required|date|before_or_equal:start_date';
            }

            $request->validate($validationRules);

            // Prepare update data
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'max_participants' => $request->max_participants ?? $event->max_participants,
                'venue_name' => $request->venue_name ?? $event->venue_name,
                'venue_address' => $request->venue_address ?? $event->venue_address,
                'city' => $request->city ?? $event->city,
                'country' => $request->country ?? $event->country,
                'registration_fee' => $request->registration_fee ?? $event->registration_fee,
            ];

            // Add registration_deadline only for standard events
            if (!$isInnovationEvent && !$isConferenceEvent) {
                $updateData['registration_deadline'] = $request->registration_deadline;
            }

            // Handle innovation event specific fields
            if ($isInnovationEvent) {
                $updateData['delivery_mode'] = $request->delivery_mode;
                $updateData['innovation_categories'] = array_values(array_filter($request->innovation_categories));
                $updateData['innovation_theme'] = array_values(array_filter($request->innovation_theme ?? []));
                
                // Add F2F specific fields - parse datetime-local format
                $updateData['f2f_start_date'] = $request->f2f_start_date ? str_replace('T', ' ', $request->f2f_start_date) : null;
                $updateData['f2f_end_date'] = $request->f2f_end_date ? str_replace('T', ' ', $request->f2f_end_date) : null;
                $updateData['f2f_start_time'] = $request->f2f_start_time;
                $updateData['f2f_end_time'] = $request->f2f_end_time;
                $updateData['f2f_registration_deadline'] = $request->f2f_registration_deadline ? str_replace('T', ' ', $request->f2f_registration_deadline) : null;
                $updateData['f2f_submission_deadline'] = $request->f2f_submission_deadline ? str_replace('T', ' ', $request->f2f_submission_deadline) : null;
                $updateData['f2f_acceptance_notification_date'] = $request->f2f_acceptance_notification_date ? str_replace('T', ' ', $request->f2f_acceptance_notification_date) : null;
                $updateData['f2f_jury_registration_deadline'] = $request->f2f_jury_registration_deadline ? str_replace('T', ' ', $request->f2f_jury_registration_deadline) : null;
                $updateData['f2f_payment_deadline'] = $request->f2f_payment_deadline_new ? str_replace('T', ' ', $request->f2f_payment_deadline_new) : null;
                
                // Populate legacy columns
                $updateData['start_date'] = $updateData['f2f_start_date'];
                $updateData['end_date'] = $updateData['f2f_end_date'];
                $updateData['start_time'] = $request->f2f_start_time;
                $updateData['end_time'] = $request->f2f_end_time;
                
                // Handle F2F extended deadlines
                $updateData['f2f_extended_registration_deadline'] = $request->f2f_extended_registration_deadline ? str_replace('T', ' ', $request->f2f_extended_registration_deadline) : null;
                $updateData['f2f_extended_jury_deadline'] = $request->f2f_extended_jury_deadline ? str_replace('T', ' ', $request->f2f_extended_jury_deadline) : null;
                $updateData['f2f_extended_submission_deadline'] = $request->f2f_extended_submission_deadline ? str_replace('T', ' ', $request->f2f_extended_submission_deadline) : null;
                $updateData['f2f_extended_notification_date'] = $request->f2f_extended_notification_date ? str_replace('T', ' ', $request->f2f_extended_notification_date) : null;
                
                // Add Online specific fields - parse datetime-local format
                $updateData['online_start_date'] = $request->online_start_date ? str_replace('T', ' ', $request->online_start_date) : null;
                $updateData['online_end_date'] = $request->online_end_date ? str_replace('T', ' ', $request->online_end_date) : null;
                $updateData['online_start_time'] = $request->online_start_time;
                $updateData['online_end_time'] = $request->online_end_time;
                $updateData['online_platform_url'] = $request->online_platform_url;
                $updateData['online_registration_deadline'] = $request->online_registration_deadline ? str_replace('T', ' ', $request->online_registration_deadline) : null;
                $updateData['online_submission_deadline'] = $request->online_submission_deadline ? str_replace('T', ' ', $request->online_submission_deadline) : null;
                $updateData['online_acceptance_notification_date_new'] = $request->online_acceptance_notification_date_new ? str_replace('T', ' ', $request->online_acceptance_notification_date_new) : null;
                $updateData['online_jury_registration_deadline_new'] = $request->online_jury_registration_deadline_new ? str_replace('T', ' ', $request->online_jury_registration_deadline_new) : null;
                $updateData['online_payment_deadline_new'] = $request->online_payment_deadline_new ? str_replace('T', ' ', $request->online_payment_deadline_new) : null;
                
                // Handle Online extended deadlines
                $updateData['online_extended_registration_deadline'] = $request->online_extended_registration_deadline ? str_replace('T', ' ', $request->online_extended_registration_deadline) : null;
                $updateData['online_extended_jury_deadline'] = $request->online_extended_jury_deadline ? str_replace('T', ' ', $request->online_extended_jury_deadline) : null;
                $updateData['online_extended_submission_deadline'] = $request->online_extended_submission_deadline ? str_replace('T', ' ', $request->online_extended_submission_deadline) : null;
                $updateData['online_extended_notification_date'] = $request->online_extended_notification_date ? str_replace('T', ' ', $request->online_extended_notification_date) : null;
            } elseif ($isConferenceEvent) {
                // Handle conference event specific fields
                $updateData['delivery_mode'] = $request->delivery_mode;
                $updateData['conference_categories'] = array_values(array_filter($request->conference_categories));
                
                // Add F2F conference fields - parse datetime-local format
                $updateData['f2f_start_date'] = $request->f2f_start_date ? str_replace('T', ' ', $request->f2f_start_date) : null;
                $updateData['f2f_end_date'] = $request->f2f_end_date ? str_replace('T', ' ', $request->f2f_end_date) : null;
                $updateData['f2f_start_time'] = $request->f2f_start_time;
                $updateData['f2f_end_time'] = $request->f2f_end_time;
                $updateData['f2f_reviewer_registration_deadline'] = $request->f2f_reviewer_registration_deadline ? str_replace('T', ' ', $request->f2f_reviewer_registration_deadline) : null;
                $updateData['f2f_paper_submission_deadline'] = $request->f2f_paper_submission_deadline ? str_replace('T', ' ', $request->f2f_paper_submission_deadline) : null;
                $updateData['f2f_review_deadline'] = $request->f2f_review_deadline ? str_replace('T', ' ', $request->f2f_review_deadline) : null;
                $updateData['f2f_acceptance_notification_date'] = $request->f2f_acceptance_notification_date ? str_replace('T', ' ', $request->f2f_acceptance_notification_date) : null;
                $updateData['f2f_payment_deadline'] = $request->f2f_payment_deadline ? str_replace('T', ' ', $request->f2f_payment_deadline) : null;
                
                // Add Online conference fields - parse datetime-local format
                $updateData['online_start_date'] = $request->online_start_date ? str_replace('T', ' ', $request->online_start_date) : null;
                $updateData['online_end_date'] = $request->online_end_date ? str_replace('T', ' ', $request->online_end_date) : null;
                $updateData['online_start_time'] = $request->online_start_time;
                $updateData['online_end_time'] = $request->online_end_time;
                $updateData['online_platform_url'] = $request->online_platform_url;
                $updateData['online_reviewer_registration_deadline'] = $request->online_reviewer_registration_deadline ? str_replace('T', ' ', $request->online_reviewer_registration_deadline) : null;
                $updateData['online_paper_submission_deadline'] = $request->online_paper_submission_deadline ? str_replace('T', ' ', $request->online_paper_submission_deadline) : null;
                $updateData['online_review_deadline'] = $request->online_review_deadline ? str_replace('T', ' ', $request->online_review_deadline) : null;
                $updateData['online_acceptance_notification_date'] = $request->online_acceptance_notification_date ? str_replace('T', ' ', $request->online_acceptance_notification_date) : null;
                $updateData['online_payment_deadline'] = $request->online_payment_deadline ? str_replace('T', ' ', $request->online_payment_deadline) : null;
                
                // Populate legacy columns
                $updateData['start_date'] = $updateData['f2f_start_date'] ?? $updateData['online_start_date'];
                $updateData['end_date'] = $updateData['f2f_end_date'] ?? $updateData['online_end_date'];
                $updateData['start_time'] = $request->f2f_start_time ?? $request->online_start_time;
                $updateData['end_time'] = $request->f2f_end_time ?? $request->online_end_time;
            } else {
                // Standard event fields
                $updateData['start_date'] = $request->start_date;
                $updateData['end_date'] = $request->end_date;
            }

            // Handle featured image upload - CLOUDINARY APPROACH
            if ($request->hasFile('featured_image')) {
                try {
                    $file = $request->file('featured_image');
                    
                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file: ' . $file->getErrorMessage());
                    }
                    
                    Log::info('Starting Cloudinary upload for event ' . $event->id);
                    
                    // Delete old image from Cloudinary if exists
                    if ($event->featured_image_public_id) {
                        $this->cloudinaryService->delete($event->featured_image_public_id, 'image');
                        Log::info('Deleted old image from Cloudinary');
                    }
                    
                    // Upload to Cloudinary
                    $uploadResult = $this->cloudinaryService->uploadImage(
                        $file,
                        'events/posters',
                        [
                            'public_id' => 'event_' . $event->id . '_poster',
                            'overwrite' => true,
                            'transformation' => [
                                'width' => 1920,
                                'height' => 1080,
                                'crop' => 'limit',
                                'quality' => 'auto:good'
                            ]
                        ]
                    );
                    
                    $updateData['featured_image'] = $uploadResult['secure_url'];
                    $updateData['featured_image_public_id'] = $uploadResult['public_id'];
                    Log::info('Image uploaded to Cloudinary: ' . $uploadResult['secure_url']);
                    
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload failed: ' . $e->getMessage());
                    Log::error('File details: ' . json_encode([
                        'name' => $request->file('featured_image')->getClientOriginalName(),
                        'size' => $request->file('featured_image')->getSize(),
                        'mime' => $request->file('featured_image')->getMimeType()
                    ]));
                    
                    return back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'Upload failed: ' . $e->getMessage()]);
                }
            }

            // Update the event
            try {
                Log::info('Attempting to update event ' . $event->id);
                Log::info('Update data: ' . json_encode($updateData));
                
                $event->update($updateData);
                
                Log::info('Event updated successfully');
                
                return redirect()->route('organizer.events.show', $event)
                    ->with('success', 'Event updated successfully!');
            } catch (\Exception $updateException) {
                Log::error('Event update failed during save: ' . $updateException->getMessage());
                Log::error('Stack trace: ' . $updateException->getTraceAsString());
                
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Database update failed: ' . $updateException->getMessage()]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update event. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Check if the event belongs to the current organizer
        $organizer = Auth::guard('organizer')->user();
        if ($event->organizer_id !== $organizer->id) {
            abort(403, 'Unauthorized');
        }

        // Delete featured image if exists
        if ($event->featured_image) {
            Storage::disk('public')->delete($event->featured_image);
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Duplicate an event
     */
    public function duplicate(Event $event)
    {
        // Check if the event belongs to the current organizer
        $organizer = Auth::guard('organizer')->user();
        if ($event->organizer_id !== $organizer->id) {
            abort(403, 'Unauthorized');
        }

        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->slug = $this->generateUniqueSlug($newEvent->title);
        $newEvent->status = 'draft';
        $newEvent->current_participants = 0;
        $newEvent->created_at = now();
        $newEvent->updated_at = now();
        
        // Set dates to future
        $newEvent->start_date = now()->addWeek();
        $newEvent->end_date = now()->addWeek()->addHours(2);
        $newEvent->registration_deadline = now()->addDays(5);

        $newEvent->save();

        return redirect()->route('organizer.events.edit', $newEvent)
            ->with('success', 'Event duplicated successfully! Please update the details.');
    }

    /**
     * Generate unique slug for event
     */
    private function generateUniqueSlug($title, $excludeId = null)
    {
        // Ensure title is not empty
        if (empty($title) || trim($title) === '') {
            $title = 'untitled-event';
        }
        
        $slug = Str::slug($title);
        
        // If slug generation fails, use a fallback
        if (empty($slug)) {
            $slug = 'event-' . time();
        }
        
        $originalSlug = $slug;
        $counter = 1;

        while (Event::where('slug', $slug)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
