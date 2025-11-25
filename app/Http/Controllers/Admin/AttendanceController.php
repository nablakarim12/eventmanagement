<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['registration.user', 'registration.event']);
        
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
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('checked_in_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('checked_in_at', '<=', $request->date_to);
        }
        
        $attendances = $query->orderBy('checked_in_at', 'desc')->paginate(15);
        $events = Event::orderBy('title')->get();
        
        // Statistics
        $stats = [
            'total_attendance' => Attendance::count(),
            'today_attendance' => Attendance::whereDate('checked_in_at', today())->count(),
            'avg_duration' => Attendance::whereNotNull('checked_out_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (checked_out_at - checked_in_at))/60) as avg_minutes')
                ->value('avg_minutes')
        ];
        
        return view('admin.attendance.index', compact('attendances', 'events', 'stats'));
    }
    
    public function event(Event $event)
    {
        $attendances = Attendance::with(['registration.user'])
            ->whereHas('registration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->orderBy('checked_in_at', 'desc')
            ->paginate(15);
            
        $stats = [
            'total_registrations' => $event->registrations()->count(),
            'total_attended' => $attendances->total(),
            'attendance_rate' => $event->registrations()->count() > 0 
                ? round(($attendances->total() / $event->registrations()->count()) * 100, 2) 
                : 0,
            'avg_duration' => Attendance::whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->whereNotNull('checked_out_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (checked_out_at - checked_in_at))/60) as avg_minutes')
                ->value('avg_minutes') ?? 0
        ];
        
        return view('admin.attendance.event', compact('event', 'attendances', 'stats'));
    }
    
    public function export(Request $request)
    {
        $query = Attendance::with(['registration.user', 'registration.event']);
        
        if ($request->has('event_id') && $request->event_id) {
            $query->whereHas('registration', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('checked_in_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('checked_in_at', '<=', $request->date_to);
        }
        
        $attendances = $query->get();
        
        $filename = 'attendance_report_' . date('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Event', 'Participant Name', 'Email', 'Check-in Time', 'Check-out Time',
                'Duration (minutes)', 'Check-in Method'
            ]);
            
            foreach ($attendances as $attendance) {
                $duration = null;
                if ($attendance->checked_out_at) {
                    $duration = round(
                        (strtotime($attendance->checked_out_at) - strtotime($attendance->checked_in_at)) / 60,
                        2
                    );
                }
                
                fputcsv($file, [
                    $attendance->registration->event->title,
                    $attendance->registration->user->name,
                    $attendance->registration->user->email,
                    $attendance->checked_in_at ? $attendance->checked_in_at->format('Y-m-d H:i:s') : '',
                    $attendance->checked_out_at ? $attendance->checked_out_at->format('Y-m-d H:i:s') : '',
                    $duration,
                    $attendance->check_in_method
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}