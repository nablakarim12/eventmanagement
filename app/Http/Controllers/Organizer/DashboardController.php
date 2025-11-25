<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    public function index()
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get event statistics
        $totalEvents = Event::where('organizer_id', $organizer->id)->count();
        $upcomingEvents = Event::where('organizer_id', $organizer->id)
            ->upcoming()
            ->published()
            ->count();
        $pastEvents = Event::where('organizer_id', $organizer->id)
            ->past()
            ->count();
        $draftEvents = Event::where('organizer_id', $organizer->id)
            ->where('status', 'draft')
            ->count();
        $cancelledEvents = Event::where('organizer_id', $organizer->id)
            ->where('status', 'cancelled')
            ->count();

        // Get total registrations across all events
        $totalRegistrations = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.organizer_id', $organizer->id)
            ->count();

        $confirmedRegistrations = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.organizer_id', $organizer->id)
            ->where('event_registrations.status', 'confirmed')
            ->count();

        $pendingRegistrations = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.organizer_id', $organizer->id)
            ->where('event_registrations.status', 'pending')
            ->count();

        // Calculate revenue statistics
        $totalRevenue = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.organizer_id', $organizer->id)
            ->where('event_registrations.payment_status', 'paid')
            ->sum('event_registrations.amount_paid');

        $pendingRevenue = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.organizer_id', $organizer->id)
            ->where('event_registrations.payment_status', 'pending')
            ->sum('events.registration_fee');

        // Get recent events
        $recentEvents = Event::where('organizer_id', $organizer->id)
            ->latest()
            ->take(5)
            ->with(['category', 'registrations' => function($query) {
                $query->select('event_id', \DB::raw('count(*) as total'))
                      ->groupBy('event_id');
            }])
            ->get();

        // Get upcoming events for calendar with registration data
        $upcomingEventsData = Event::where('organizer_id', $organizer->id)
            ->upcoming()
            ->published()
            ->orderBy('start_date')
            ->take(10)
            ->with(['registrations' => function($query) {
                $query->select('event_id', \DB::raw('count(*) as total'))
                      ->groupBy('event_id');
            }])
            ->get();

        // Get top performing events (by registration count)
        $topEvents = Event::where('organizer_id', $organizer->id)
            ->leftJoin('event_registrations', 'events.id', '=', 'event_registrations.event_id')
            ->select('events.*', \DB::raw('COUNT(event_registrations.id) as registrations_count'))
            ->groupBy('events.id')
            ->havingRaw('COUNT(event_registrations.id) > 0')
            ->orderByRaw('COUNT(event_registrations.id) DESC')
            ->take(5)
            ->get();

        // Monthly event and registration statistics for charts
        $monthlyStats = [];
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'events' => Event::where('organizer_id', $organizer->id)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
                'registrations' => \DB::table('event_registrations')
                    ->join('events', 'event_registrations.event_id', '=', 'events.id')
                    ->where('events.organizer_id', $organizer->id)
                    ->whereMonth('event_registrations.created_at', $month->month)
                    ->whereYear('event_registrations.created_at', $month->year)
                    ->count()
            ];

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => \DB::table('event_registrations')
                    ->join('events', 'event_registrations.event_id', '=', 'events.id')
                    ->where('events.organizer_id', $organizer->id)
                    ->where('event_registrations.payment_status', 'paid')
                    ->whereMonth('event_registrations.created_at', $month->month)
                    ->whereYear('event_registrations.created_at', $month->year)
                    ->sum('event_registrations.amount_paid') ?: 0
            ];
        }

        // Category performance
        $categoryStats = Event::where('organizer_id', $organizer->id)
            ->with(['category', 'registrations'])
            ->get()
            ->groupBy('category.name')
            ->map(function ($events, $categoryName) {
                return [
                    'category' => $categoryName ?: 'Uncategorized',
                    'events_count' => $events->count(),
                    'total_registrations' => $events->sum(function($event) {
                        return $event->registrations->count();
                    }),
                    'avg_registrations' => $events->avg(function($event) {
                        return $event->registrations->count();
                    })
                ];
            })
            ->values();

        // Recent activity feed
        $recentActivity = collect();

        // Recent registrations
        $recentRegistrations = \DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('events.organizer_id', $organizer->id)
            ->select(
                'event_registrations.created_at',
                'events.title as event_title',
                'users.name as user_name',
                'event_registrations.status',
                \DB::raw("'registration' as type")
            )
            ->latest('event_registrations.created_at')
            ->take(10)
            ->get();

        $recentActivity = $recentActivity->merge($recentRegistrations);

        // Recent event updates
        $recentEventUpdates = Event::where('organizer_id', $organizer->id)
            ->select('updated_at as created_at', 'title as event_title', 'status', \DB::raw("'event_update' as type"))
            ->latest('updated_at')
            ->take(10)
            ->get();

        $recentActivity = $recentActivity->merge($recentEventUpdates)
            ->sortByDesc('created_at')
            ->take(15);

        return view('organizer.dashboard', compact(
            'organizer',
            'totalEvents',
            'upcomingEvents',
            'pastEvents',
            'draftEvents',
            'cancelledEvents',
            'totalRegistrations',
            'confirmedRegistrations',
            'pendingRegistrations',
            'totalRevenue',
            'pendingRevenue',
            'recentEvents',
            'upcomingEventsData',
            'topEvents',
            'monthlyStats',
            'monthlyRevenue',
            'categoryStats',
            'recentActivity'
        ));
    }
}
