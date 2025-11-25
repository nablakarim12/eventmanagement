<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = EventMaterial::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with('event');

        // Filter by event if specified
        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('file_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by file type
        if ($request->file_type) {
            $query->where('file_type', $request->file_type);
        }

        $materials = $query->latest()->paginate(15);
        
        $events = Event::where('organizer_id', $organizer->id)->get();
        
        $fileTypes = EventMaterial::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->distinct()->pluck('file_type');

        return view('organizer.materials.index', compact('materials', 'events', 'fileTypes'));
    }

    public function create(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        $events = Event::where('organizer_id', $organizer->id)->get();
        
        $selectedEvent = null;
        if ($request->event_id) {
            $selectedEvent = Event::where('organizer_id', $organizer->id)
                ->findOrFail($request->event_id);
        }

        return view('organizer.materials.create', compact('events', 'selectedEvent'));
    }

    public function store(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|max:51200', // 50MB max
            'access_type' => 'required|in:public,registered_only,checked_in_only',
            'is_downloadable' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        // Verify organizer owns the event
        $event = Event::where('organizer_id', $organizer->id)
            ->findOrFail($request->event_id);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('event_materials', $fileName, 'public');

        EventMaterial::create([
            'event_id' => $event->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'access_type' => $request->access_type,
            'is_downloadable' => $request->boolean('is_downloadable', true),
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
        ]);

        return redirect()->route('organizer.materials.index')
            ->with('success', 'Material uploaded successfully!');
    }

    public function show(EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Verify organizer owns the material through event
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $material->load('event');
        
        return view('organizer.materials.show', compact('material'));
    }

    public function edit(EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Verify organizer owns the material
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $events = Event::where('organizer_id', $organizer->id)->get();
        
        return view('organizer.materials.edit', compact('material', 'events'));
    }

    public function update(Request $request, EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Verify organizer owns the material
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:51200',
            'access_type' => 'required|in:public,registered_only,checked_in_only',
            'is_downloadable' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'access_type' => $request->access_type,
            'is_downloadable' => $request->boolean('is_downloadable'),
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($material->file_path);
            
            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('event_materials', $fileName, 'public');
            
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_path'] = $filePath;
            $updateData['file_type'] = $file->getClientOriginalExtension();
            $updateData['file_size'] = $file->getSize();
        }

        $material->update($updateData);

        return redirect()->route('organizer.materials.show', $material)
            ->with('success', 'Material updated successfully!');
    }

    public function destroy(EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Verify organizer owns the material
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($material->file_path);
        
        $material->delete();

        return redirect()->route('organizer.materials.index')
            ->with('success', 'Material deleted successfully!');
    }

    public function download(EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Verify organizer owns the material
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        if (!$material->is_available || !$material->is_downloadable) {
            abort(404, 'File not available for download');
        }

        $material->incrementDownloadCount();

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }

    public function analytics(EventMaterial $material)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($material->event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Here you can add detailed analytics for material downloads, views, etc.
        return view('organizer.materials.analytics', compact('material'));
    }
}
