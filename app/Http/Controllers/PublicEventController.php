<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCategory;
use Carbon\Carbon;

class PublicEventController extends Controller
{
    /**
     * Display a listing of public events
     */
    public function index(Request $request)
    {
        $query = Event::with(['category', 'organizer'])
            ->where('status', 'published')
            ->where('is_public', true);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('city', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('venue_name', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Date filter
        if ($request->has('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('start_date', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('start_date', Carbon::tomorrow());
                    break;
                case 'this_week':
                    $query->whereBetween('start_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('start_date', Carbon::now()->month)
                          ->whereYear('start_date', Carbon::now()->year);
                    break;
                case 'upcoming':
                    $query->where('start_date', '>=', Carbon::now());
                    break;
            }
        } else {
            // Default: show upcoming events
            $query->where('start_date', '>=', Carbon::now());
        }

        // Location filter
        if ($request->has('city') && $request->city) {
            $query->where('city', 'ILIKE', "%{$request->city}%");
        }

        // Price filter
        if ($request->has('price_filter')) {
            switch ($request->price_filter) {
                case 'free':
                    $query->where(function($q) {
                        $q->where('registration_fee', 0)->orWhere('is_free', true);
                    });
                    break;
                case 'paid':
                    $query->where('registration_fee', '>', 0)->where('is_free', false);
                    break;
                case 'under_50':
                    $query->where('registration_fee', '>', 0)
                          ->where('registration_fee', '<', 50)
                          ->where('is_free', false);
                    break;
                case 'under_100':
                    $query->where('registration_fee', '>', 0)
                          ->where('registration_fee', '<', 100)
                          ->where('is_free', false);
                    break;
            }
        }

        // City filter
        if ($request->has('city') && $request->city) {
            $query->where('city', 'ILIKE', "%{$request->city}%");
        }

        // Location filter (venue)
        if ($request->has('location') && $request->location) {
            $query->where('venue_name', 'ILIKE', "%{$request->location}%");
        }

                // Sorting
        $sort = $request->get('sort', 'start_date');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'price':
                $query->orderBy('registration_fee', 'asc');
                break;
            case 'created':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('start_date', 'asc');
                break;
        }

        $events = $query->paginate(12);
        $categories = EventCategory::orderBy('name')->get();

        return view('public.events.index', compact('events', 'categories'));
    }

    /**
     * Show a specific event
     */
    public function show($slug)
    {
        $event = Event::with(['category', 'organizer'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('is_public', true)
            ->firstOrFail();

        // Get related events from same category
        $relatedEvents = Event::with(['category', 'organizer'])
            ->where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->where('start_date', '>=', Carbon::now())
            ->limit(3)
            ->get();

        return view('public.events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Get events by category
     */
    public function byCategory(Request $request, $categorySlug)
    {
        $category = EventCategory::where('slug', $categorySlug)->firstOrFail();
        
        $query = Event::with(['category', 'organizer'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->where('is_public', true);

        // Search within category
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('venue_name', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Date filter
        if ($request->has('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('start_date', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('start_date', Carbon::tomorrow());
                    break;
                case 'this_week':
                    $query->whereBetween('start_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('start_date', Carbon::now()->month)
                          ->whereYear('start_date', Carbon::now()->year);
                    break;
                default:
                    $query->where('start_date', '>=', Carbon::now());
                    break;
            }
        } else {
            $query->where('start_date', '>=', Carbon::now());
        }

        // Price filter
        if ($request->has('price_filter')) {
            switch ($request->price_filter) {
                case 'free':
                    $query->where(function($q) {
                        $q->where('registration_fee', 0)->orWhere('is_free', true);
                    });
                    break;
                case 'paid':
                    $query->where('registration_fee', '>', 0)->where('is_free', false);
                    break;
                case 'under_50':
                    $query->where('registration_fee', '>', 0)
                          ->where('registration_fee', '<', 50)
                          ->where('is_free', false);
                    break;
                case 'under_100':
                    $query->where('registration_fee', '>', 0)
                          ->where('registration_fee', '<', 100)
                          ->where('is_free', false);
                    break;
            }
        }

        // Sorting
        $sort = $request->get('sort', 'start_date');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'price':
                $query->orderBy('registration_fee', 'asc');
                break;
            case 'created':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('start_date', 'asc');
                break;
        }

        $events = $query->paginate(12);
        
        // Get related categories
        $relatedCategories = EventCategory::where('id', '!=', $category->id)
            ->withCount('events')
            ->limit(5)
            ->get();

        return view('public.events.category', compact('events', 'category', 'relatedCategories'));
    }

    /**
     * Search events via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $events = Event::with(['category'])
            ->where('status', 'published')
            ->where('is_public', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ILIKE', "%{$query}%")
                  ->orWhere('description', 'ILIKE', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'category' => $event->category->name,
                    'date' => $event->start_date->format('M j, Y'),
                    'city' => $event->city,
                ];
            });

        return response()->json(['results' => $events]);
    }
}
