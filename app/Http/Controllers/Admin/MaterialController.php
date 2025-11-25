<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventMaterial;
use App\Models\Event;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = EventMaterial::with(['event', 'event.organizer']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhereHas('event', function($eventQuery) use ($search) {
                      $eventQuery->where('title', 'ILIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        $materials = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = Event::orderBy('title')->get();
        
        return view('admin.materials.index', compact('materials', 'events'));
    }
    
    public function show(EventMaterial $material)
    {
        $material->load(['event', 'event.organizer']);
        return view('admin.materials.show', compact('material'));
    }
    
    public function download(EventMaterial $material)
    {
        return response()->download(storage_path('app/' . $material->file_path));
    }
    
    public function destroy(EventMaterial $material)
    {
        // Delete the file from storage
        if (\Storage::exists($material->file_path)) {
            \Storage::delete($material->file_path);
        }
        
        $material->delete();
        
        return redirect()->route('admin.materials.index')
            ->with('success', 'Material deleted successfully.');
    }
}