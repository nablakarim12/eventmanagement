<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Event;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['registration.user', 'registration.event']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('registration', function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'ILIKE', "%{$search}%")
                             ->orWhere('email', 'ILIKE', "%{$search}%");
                })->orWhereHas('event', function($eventQuery) use ($search) {
                    $eventQuery->where('title', 'ILIKE', "%{$search}%");
                });
            });
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->whereHas('registration', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        // Filter by generation date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $certificates = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = Event::orderBy('title')->get();
        
        // Statistics
        $stats = [
            'total_certificates' => Certificate::count(),
            'certificates_today' => Certificate::whereDate('created_at', today())->count(),
            'certificates_this_month' => Certificate::whereMonth('created_at', date('m'))
                                                  ->whereYear('created_at', date('Y'))
                                                  ->count(),
        ];
        
        return view('admin.certificates.index', compact('certificates', 'events', 'stats'));
    }
    
    public function show(Certificate $certificate)
    {
        $certificate->load(['registration.user', 'registration.event']);
        return view('admin.certificates.show', compact('certificate'));
    }
    
    public function download(Certificate $certificate)
    {
        return response()->download(storage_path('app/' . $certificate->file_path));
    }
    
    public function destroy(Certificate $certificate)
    {
        // Delete the file from storage
        if (\Storage::exists($certificate->file_path)) {
            \Storage::delete($certificate->file_path);
        }
        
        $certificate->delete();
        
        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }
}