<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show analytics dashboard
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Date range filter (default to last 6 months)
        $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Event Performance Analytics
        $eventPerformance = Event::where('organizer_id', $organizer->id)
            ->with(['registrations' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($event) {
                $registrations = $event->registrations;
                $revenue = $registrations->where('payment_status', 'paid')->sum('amount_paid');
                $conversionRate = $event->views > 0 ? ($registrations->count() / $event->views) * 100 : 0;
                
                return [
                    'event' => $event,
                    'registrations_count' => $registrations->count(),
                    'confirmed_count' => $registrations->where('status', 'confirmed')->count(),
                    'attended_count' => $registrations->where('status', 'attended')->count(),
                    'revenue' => $revenue,
                    'conversion_rate' => round($conversionRate, 2),
                    'roi' => $event->budget > 0 ? round(($revenue / $event->budget) * 100, 2) : 0
                ];
            })
            ->sortByDesc('registrations_count');

        // Registration Trends
        $registrationTrends = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current <= $end) {
            $registrationCount = EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->whereDate('created_at', $current)
            ->count();
            
            $registrationTrends[] = [
                'date' => $current->format('Y-m-d'),
                'count' => $registrationCount
            ];
            
            $current->addDay();
        }

        // Revenue Analytics
        $revenueByMonth = EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Events by Revenue
        $topEventsByRevenue = Event::where('organizer_id', $organizer->id)
            ->with(['registrations' => function($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($event) {
                return [
                    'event' => $event,
                    'revenue' => $event->registrations->sum('amount_paid'),
                    'paid_registrations' => $event->registrations->count()
                ];
            })
            ->sortByDesc('revenue')
            ->take(10);

        // Demographic Analytics
        $demographics = $this->getParticipantDemographics($organizer->id, $startDate, $endDate);
        
        // Registration Status Distribution
        $statusDistribution = EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Category Performance
        $categoryPerformance = Event::where('organizer_id', $organizer->id)
            ->with(['category', 'registrations' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->groupBy('category.name')
            ->map(function($events, $categoryName) {
                $totalRegistrations = $events->sum(function($event) {
                    return $event->registrations->count();
                });
                $totalRevenue = $events->sum(function($event) {
                    return $event->registrations->where('payment_status', 'paid')->sum('amount_paid');
                });
                
                return [
                    'category' => $categoryName ?: 'Uncategorized',
                    'events_count' => $events->count(),
                    'total_registrations' => $totalRegistrations,
                    'total_revenue' => $totalRevenue,
                    'avg_registrations_per_event' => $events->count() > 0 ? round($totalRegistrations / $events->count(), 1) : 0
                ];
            })
            ->values()
            ->sortByDesc('total_revenue');

        // Attendance Analytics
        $attendanceRate = $this->getAttendanceAnalytics($organizer->id, $startDate, $endDate);

        return view('organizer.analytics.index', compact(
            'eventPerformance',
            'registrationTrends',
            'revenueByMonth',
            'topEventsByRevenue',
            'demographics',
            'statusDistribution',
            'categoryPerformance',
            'attendanceRate',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get participant demographics
     */
    private function getParticipantDemographics($organizerId, $startDate, $endDate)
    {
        // This would require additional user profile fields
        // For now, return basic statistics
        
        $registrations = EventRegistration::whereHas('event', function($q) use ($organizerId) {
                $q->where('organizer_id', $organizerId);
            })
            ->with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Email domain analysis (basic demographic insight)
        $emailDomains = $registrations->groupBy(function($registration) {
                return substr(strrchr($registration->user->email, "@"), 1);
            })
            ->map->count()
            ->sortDesc()
            ->take(10);

        // Registration timing analysis
        $hourlyDistribution = $registrations->groupBy(function($registration) {
                return $registration->created_at->format('H');
            })
            ->map->count()
            ->sortKeys();

        return [
            'total_participants' => $registrations->count(),
            'unique_participants' => $registrations->unique('user_id')->count(),
            'repeat_participants' => $registrations->count() - $registrations->unique('user_id')->count(),
            'email_domains' => $emailDomains,
            'hourly_distribution' => $hourlyDistribution
        ];
    }

    /**
     * Get attendance analytics
     */
    private function getAttendanceAnalytics($organizerId, $startDate, $endDate)
    {
        $events = Event::where('organizer_id', $organizerId)
            ->where('end_date', '<', now()) // Only completed events
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with('registrations')
            ->get();

        $totalConfirmed = 0;
        $totalAttended = 0;

        foreach ($events as $event) {
            $confirmed = $event->registrations->where('status', 'confirmed')->count();
            $attended = $event->registrations->where('status', 'attended')->count();
            
            $totalConfirmed += $confirmed;
            $totalAttended += $attended;
        }

        return [
            'events_completed' => $events->count(),
            'total_confirmed' => $totalConfirmed,
            'total_attended' => $totalAttended,
            'attendance_rate' => $totalConfirmed > 0 ? round(($totalAttended / $totalConfirmed) * 100, 1) : 0,
            'no_show_rate' => $totalConfirmed > 0 ? round((($totalConfirmed - $totalAttended) / $totalConfirmed) * 100, 1) : 0
        ];
    }

    /**
     * Export analytics report
     */
    public function export(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $events = Event::where('organizer_id', $organizer->id)
            ->with(['registrations' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        $filename = 'analytics_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Event Title',
                'Event Date',
                'Total Registrations',
                'Confirmed Registrations',
                'Attended Count',
                'Revenue',
                'Attendance Rate (%)',
                'Category'
            ]);

            // CSV Data
            foreach ($events as $event) {
                $registrations = $event->registrations;
                $confirmed = $registrations->where('status', 'confirmed')->count();
                $attended = $registrations->where('status', 'attended')->count();
                $revenue = $registrations->where('payment_status', 'paid')->sum('amount_paid');
                $attendanceRate = $confirmed > 0 ? round(($attended / $confirmed) * 100, 1) : 0;
                
                fputcsv($file, [
                    $event->title,
                    $event->start_date,
                    $registrations->count(),
                    $confirmed,
                    $attended,
                    $revenue,
                    $attendanceRate,
                    $event->category->name ?? 'Uncategorized'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}