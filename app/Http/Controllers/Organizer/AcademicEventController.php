<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Services\AcademicAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Academic Event Management Controller
 * 
 * Manages academic conferences and innovation competitions
 * Uses QR attendance to enable/disable other functions
 */
class AcademicEventController extends Controller
{
    protected $attendanceService;
    
    public function __construct(AcademicAttendanceService $attendanceService)
    {
        $this->middleware('auth:organizer');
        $this->attendanceService = $attendanceService;
    }
    
    /**
     * Judging Dashboard - Only accessible to present jury
     */
    public function judgingDashboard(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        // Get only present jury members who can judge
        $presentJury = $this->attendanceService->getPresentJury($event);
        $presentParticipants = $this->attendanceService->getPresentParticipants($event);
        
        // Get attendance analytics
        $analytics = $this->attendanceService->getAttendanceAnalytics($event);
        
        return view('organizer.academic.judging-dashboard', compact(
            'event', 'presentJury', 'presentParticipants', 'analytics'
        ));
    }
    
    /**
     * Competition Status - Track who can compete
     */
    public function competitionStatus(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        $participants = User::whereHas('registrations', function($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->where('registration_type', 'participant');
        })->with(['attendance' => function($query) use ($event) {
            $query->where('event_id', $event->id);
        }])->get();
        
        $competitionStatus = [];
        foreach ($participants as $participant) {
            $canCompete = $this->attendanceService->canParticipantCompete($participant, $event);
            $hasCompleted = $this->attendanceService->hasCompletedAttendance($participant, $event);
            
            $competitionStatus[] = [
                'participant' => $participant,
                'can_compete' => $canCompete,
                'completed_attendance' => $hasCompleted,
                'status' => $this->getParticipantStatus($participant, $event)
            ];
        }
        
        return view('organizer.academic.competition-status', compact(
            'event', 'competitionStatus'
        ));
    }
    
    /**
     * Certificate Generation - Only for completed attendance
     */
    public function certificateGeneration(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        // Get users eligible for certificates (completed attendance)
        $eligibleParticipants = User::whereHas('attendance', function($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->where('role', 'participant')
                  ->where('status', 'completed');
        })->get();
        
        $eligibleJury = User::whereHas('attendance', function($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->where('role', 'jury')
                  ->where('status', 'completed');
        })->get();
        
        return view('organizer.academic.certificate-generation', compact(
            'event', 'eligibleParticipants', 'eligibleJury'
        ));
    }
    
    /**
     * Real-time Attendance Monitor
     */
    public function attendanceMonitor(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        $analytics = $this->attendanceService->getAttendanceAnalytics($event);
        
        // Real-time status
        $liveStatus = [
            'jury_present' => $this->attendanceService->getPresentJury($event)->count(),
            'participants_present' => $this->attendanceService->getPresentParticipants($event)->count(),
            'judging_ready' => $analytics['present_jury'] >= 3, // Minimum 3 jury
            'competition_ready' => $analytics['present_participants'] >= 1
        ];
        
        return view('organizer.academic.attendance-monitor', compact(
            'event', 'analytics', 'liveStatus'
        ));
    }
    
    private function getParticipantStatus($participant, $event)
    {
        if ($this->attendanceService->hasCompletedAttendance($participant, $event)) {
            return 'completed';
        } elseif ($this->attendanceService->canParticipantCompete($participant, $event)) {
            return 'present';
        } else {
            return 'absent';
        }
    }
}