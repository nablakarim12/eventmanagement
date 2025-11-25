<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
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
    public function create()
    {
        $categories = EventCategory::all();
        return view('organizer.events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $organizer = Auth::guard('organizer')->user();

            // Basic validation including image upload
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:event_categories,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'registration_deadline' => 'required|date|before_or_equal:start_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'venue_name' => 'required|string|max:255',
                'venue_address' => 'required|string',
                'city' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Create event with minimal data
            $registrationFee = (float)($request->registration_fee ?? 0);
            
            $event = new Event([
                'organizer_id' => $organizer->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date . ' ' . $request->start_time,
                'end_date' => $request->end_date . ' ' . $request->end_time,
                'registration_deadline' => $request->registration_deadline,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'venue_name' => $request->venue_name,
                'venue_address' => $request->venue_address,
                'city' => $request->city,
                'country' => $request->country,
                'slug' => Str::slug($request->title . '-' . time()),
                'status' => $request->status ?? 'draft',
                'registration_fee' => $registrationFee,
            ]);
            
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
                    
                    Log::info('Starting image upload for new event ' . $event->id);
                    
                    // Generate filename manually
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'event_' . $event->id . '_' . time() . '.' . $extension;
                    
                    // Create directory if it doesn't exist
                    $publicPath = public_path('storage/events/posters');
                    if (!file_exists($publicPath)) {
                        mkdir($publicPath, 0777, true);
                    }
                    
                    // Move file manually
                    $destinationPath = $publicPath . '/' . $filename;
                    
                    if ($file->move($publicPath, $filename)) {
                        $event->featured_image = 'events/posters/' . $filename;
                        $event->save(); // Update the event with the image path
                        Log::info('Image stored manually at: events/posters/' . $filename);
                    } else {
                        Log::warning('Failed to move uploaded file for event ' . $event->id);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Image upload failed during event creation: ' . $e->getMessage());
                    // Don't fail the entire event creation for image upload issues
                }
            }

            return redirect()->route('organizer.events.index')
                ->with('success', 'Event created successfully!');

        } catch (\Exception $e) {
            \Log::error('Event creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
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

            // Validation including image upload
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:event_categories,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'registration_deadline' => 'required|date|before_or_equal:start_date',
                'status' => 'required|in:draft,published,cancelled',
                'location' => 'nullable|string|max:255',
                'venue_address' => 'nullable|string|max:500',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Prepare update data
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'registration_deadline' => $request->registration_deadline,
                'status' => $request->status,
                'max_participants' => $request->max_participants ?? $event->max_participants,
                'venue_name' => $request->location ?? $event->venue_name,
                'venue_address' => $request->venue_address ?? $event->venue_address,
                'registration_fee' => $request->price ?? $event->registration_fee,
            ];

            // Handle featured image upload - MANUAL APPROACH
            if ($request->hasFile('featured_image')) {
                try {
                    $file = $request->file('featured_image');
                    
                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file: ' . $file->getErrorMessage());
                    }
                    
                    Log::info('Starting manual image upload for event ' . $event->id);
                    
                    // Generate filename manually
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'event_' . $event->id . '_' . time() . '.' . $extension;
                    
                    // Create directory if it doesn't exist
                    $publicPath = public_path('storage/events/posters');
                    if (!file_exists($publicPath)) {
                        mkdir($publicPath, 0777, true);
                    }
                    
                    // Move file manually
                    $destinationPath = $publicPath . '/' . $filename;
                    
                    if ($file->move($publicPath, $filename)) {
                        $updateData['featured_image'] = 'events/posters/' . $filename;
                        Log::info('Image stored manually at: events/posters/' . $filename);
                    } else {
                        throw new \Exception('Failed to move uploaded file');
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Manual image upload failed: ' . $e->getMessage());
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
            $event->update($updateData);

            return redirect()->route('organizer.events.show', $event)
                ->with('success', 'Event updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage());
            
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
