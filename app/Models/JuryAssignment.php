<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JuryAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_submission_id',
        'participant_registration_id',
        'jury_registration_id',
        'assigned_by',
        'status',
        'assigned_at',
        'accepted_at',
        'declined_at',
        'completed_at',
        'decline_reason',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->assigned_at)) {
                $assignment->assigned_at = now();
            }
        });
    }

    /**
     * Get the paper submission
     */
    public function paperSubmission(): BelongsTo
    {
        return $this->belongsTo(PaperSubmission::class);
    }

    /**
     * Get the participant registration (for Innovation Competitions)
     */
    public function participantRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'participant_registration_id');
    }

    /**
     * Get the jury member (registration)
     */
    public function juryRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'jury_registration_id');
    }

    /**
     * Get the organizer who assigned
     */
    public function assignedByOrganizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'assigned_by');
    }

    /**
     * Get the review for this assignment
     */
    public function review(): HasOne
    {
        return $this->hasOne(PaperReview::class);
    }

    /**
     * Accept the assignment
     */
    public function accept(): void
    {
        $this->status = 'accepted';
        $this->accepted_at = now();
        $this->save();
    }

    /**
     * Decline the assignment
     */
    public function decline(string $reason = null): void
    {
        $this->status = 'declined';
        $this->declined_at = now();
        $this->decline_reason = $reason;
        $this->save();
    }

    /**
     * Mark as completed
     */
    public function complete(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Scope for pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted assignments
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
