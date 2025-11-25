<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the organizer dashboard.
     */
    public function index()
    {
        $organizer = Auth::guard('event-organizer')->user();
        return view('organizer.dashboard', compact('organizer'));
    }
}
