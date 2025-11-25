<?php

namespace App\Services;

use App\Models\EventAttendance;
use App\Models\EventQrCode; 
use App\Models\User;
use App\Models\Event;

/**
 * Academic Conference QR Attendance Service
 * 
 * Manages QR attendance for academic conferences and innovation competitions
 * Enables other functions based on attendance status
 */
class AcademicAttendanceService
{
    /**
     * Check if jury member is present and can judge
     */
    public function canJuryJudge(User $jury, Event $event): bool
    {
        $attendance = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $jury->id)
            ->where('role', 'jury')
            ->where('status', 'present')
            ->exists();
            
        return $attendance;
    }
    
    /**
     * Check if participant is present and can present/compete
     */
    public function canParticipantCompete(User $participant, Event $event): bool
    {
        $attendance = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $participant->id)
            ->where('role', 'participant')
            ->where('status', 'present')
            ->exists();
            
        return $attendance;
    }
    
    /**
     * Get all present jury members for an event
     */
    public function getPresentJury(Event $event)
    {
        return User::whereHas('attendance', function($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->where('role', 'jury')
                  ->where('status', 'present');
        })->get();
    }
    
    /**
     * Get all present participants for an event
     */
    public function getPresentParticipants(Event $event)
    {
        return User::whereHas('attendance', function($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->where('role', 'participant')
                  ->where('status', 'present');
        })->get();
    }
    
    /**
     * Check if participant completed full attendance (check-in + check-out)
     * Required for certificate generation
     */
    public function hasCompletedAttendance(User $user, Event $event): bool
    {
        return EventAttendance::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('status', 'completed') // Both check-in and check-out
            ->exists();
    }
    
    /**
     * Get attendance analytics for event organizers
     */
    public function getAttendanceAnalytics(Event $event): array
    {
        $totalRegistered = $event->registrations()->count();
        $presentJury = $this->getPresentJury($event)->count();
        $presentParticipants = $this->getPresentParticipants($event)->count();
        $completedAttendance = EventAttendance::where('event_id', $event->id)
            ->where('status', 'completed')->count();
            
        return [
            'total_registered' => $totalRegistered,
            'present_jury' => $presentJury,
            'present_participants' => $presentParticipants,
            'completed_attendance' => $completedAttendance,
            'attendance_rate' => $totalRegistered > 0 ? 
                round((($presentJury + $presentParticipants) / $totalRegistered) * 100, 2) : 0
        ];
    }
    
    /**
     * Enable/disable features based on attendance
     */
    public function getEnabledFeatures(User $user, Event $event): array
    {
        $canJudge = $this->canJuryJudge($user, $event);
        $canCompete = $this->canParticipantCompete($user, $event);
        $canGetCertificate = $this->hasCompletedAttendance($user, $event);
        
        return [
            'judging_enabled' => $canJudge,
            'presentation_enabled' => $canCompete,
            'scoring_enabled' => $canJudge || $canCompete,
            'certificate_eligible' => $canGetCertificate,
            'results_access' => $canJudge || $canCompete
        ];
    }
}