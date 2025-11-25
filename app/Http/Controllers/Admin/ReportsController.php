<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Organizer statistics
        $organizerStats = [
            'total' => EventOrganizer::count(),
            'pending' => EventOrganizer::where('status', 'pending')->count(),
            'approved' => EventOrganizer::where('status', 'approved')->count(),
            'rejected' => EventOrganizer::where('status', 'rejected')->count(),
        ];

        // Registration trends (monthly)
        $registrationTrends = EventOrganizer::select(
            DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Category statistics
        // TODO: Add ->withCount('events') when Event model is created
        $categoryStats = EventCategory::all(); // Simplified to avoid boolean issues

        return view('admin.reports.index', compact(
            'organizerStats',
            'registrationTrends',
            'categoryStats'
        ));
    }

    /**
     * Export organizers report.
     */
    public function exportOrganizers()
    {
        $organizers = EventOrganizer::all();
        
        $csv = "Organization Name,Email,Contact Person,Status,Registered Date,Approved Date\n";
        
        foreach ($organizers as $organizer) {
            $csv .= "\"{$organizer->org_name}\",";
            $csv .= "\"{$organizer->org_email}\",";
            $csv .= "\"{$organizer->contact_person_name}\",";
            $csv .= "\"{$organizer->status}\",";
            $csv .= "\"{$organizer->created_at->format('Y-m-d')}\",";
            $csv .= '"' . ($organizer->approved_at ? $organizer->approved_at->format('Y-m-d') : 'N/A') . "\"\n";
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="organizers_report.csv"');
    }
}
