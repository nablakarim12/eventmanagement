<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch dashboard statistics
        $stats = [
            'total_events' => 0, // Will be updated when Event model is created
            'active_events' => 0, // Will be updated when Event model is created
            'total_participants' => 0, // Will be updated when Participant model is created
            'total_organizers' => EventOrganizer::count(),
            'pending_organizers' => EventOrganizer::where('status', 'pending')->count(),
            'approved_organizers' => EventOrganizer::where('status', 'approved')->count(),
            'rejected_organizers' => EventOrganizer::where('status', 'rejected')->count(),
        ];

        // Fetch recent organizers
        $recentOrganizers = EventOrganizer::latest()
            ->take(5)
            ->get();

        // Fetch categories for chart (simplified for now)
        $categories = collect(); // Empty collection to avoid query issues

        return view('admin.dashboard', compact('stats', 'recentOrganizers', 'categories'));
    }
}