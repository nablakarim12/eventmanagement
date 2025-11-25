<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventQrCode;
use App\Models\Event;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = EventQrCode::with(['event', 'event.organizer']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('qr_code', 'ILIKE', "%{$search}%")
                  ->orWhere('type', 'ILIKE', "%{$search}%")
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
        
        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = Event::orderBy('title')->get();
        
        return view('admin.qr-codes.index', compact('qrCodes', 'events'));
    }
    
    public function show(EventQrCode $qrCode)
    {
        $qrCode->load(['event', 'event.organizer']);
        
        // Get scan statistics
        $scanStats = [
            'total_scans' => $qrCode->scan_count,
            'last_scan' => $qrCode->last_scanned_at
        ];
        
        return view('admin.qr-codes.show', compact('qrCode', 'scanStats'));
    }
    
    public function destroy(EventQrCode $qrCode)
    {
        $qrCode->delete();
        
        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR Code deleted successfully.');
    }
}