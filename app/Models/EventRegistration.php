<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_code',
        'qr_code',
        'qr_image_path',
        'role', // participant, jury, both
        // Jury qualification fields (nullable)
        'jury_qualification_summary',
        'jury_qualification_documents',
        'jury_experience',
        'jury_expertise_areas',
        'jury_institution',
        'jury_position',
        'jury_years_experience',
        // Legacy certificate fields (for compatibility with friend's system)
        'certificate_path',
        'certificate_filename',
        'status',
        'approval_status', // pending, approved, rejected
        'amount_paid',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'registration_data',
        'special_requirements',
        'dietary_restrictions',
        'emergency_contact_name',
        'emergency_contact_phone',
        'registered_at',
        'confirmed_at',
        'cancelled_at',
        'attended_at',
        'approved_at',
        'rejected_at',
        'approved_by', // organizer who approved
        'rejected_reason', // reason for rejection
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'registration_data' => 'array',
        'jury_qualification_documents' => 'array',
        'jury_years_experience' => 'integer',
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attended_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (empty($registration->registration_code)) {
                $registration->registration_code = 'REG-' . Str::upper(Str::random(8));
            }
            
            if (empty($registration->registered_at)) {
                $registration->registered_at = now();
            }
        });
    }

    /**
     * Get the event for this registration
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user for this registration
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organizer who approved/rejected this registration
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Organizer::class, 'approved_by');
    }

    /**
     * Get jury assignments (if registered as jury)
     */
    public function juryAssignments(): HasMany
    {
        return $this->hasMany(JuryAssignment::class, 'jury_registration_id');
    }

    /**
     * Alternative naming for jury assignments when used as jury
     */
    public function juryAssignmentsAsJury(): HasMany
    {
        return $this->hasMany(JuryAssignment::class, 'jury_registration_id');
    }

    /**
     * Get jury assignments where this registration is the participant being evaluated
     */
    public function juryAssignmentsAsParticipant(): HasMany
    {
        return $this->hasMany(JuryAssignment::class, 'participant_registration_id');
    }

    /**
     * Get paper reviews (if registered as jury)
     */
    public function paperReviews(): HasMany
    {
        return $this->hasMany(PaperReview::class, 'jury_registration_id');
    }

    /**
     * Scope for confirmed registrations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved registrations
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope for pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope for participant role
     */
    public function scopeParticipants($query)
    {
        return $query->whereIn('role', ['participant', 'both']);
    }

    /**
     * Scope for jury role
     */
    public function scopeJury($query)
    {
        return $query->whereIn('role', ['jury', 'both']);
    }

    /**
     * Scope for paid registrations
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Check if registration is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if registration is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if registration is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if registration is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Check if user registered as participant
     */
    public function isParticipant(): bool
    {
        return in_array($this->role, ['participant', 'both']);
    }

    /**
     * Check if user registered as jury
     */
    public function isJury(): bool
    {
        return in_array($this->role, ['jury', 'both']);
    }

    /**
     * Check if registration is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if registration is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if registration is eligible for QR code generation
     */
    public function isEligibleForQr(): bool
    {
        return $this->isApproved() && 
               !$this->isCancelled() && 
               ($this->event->registration_deadline ? 
                $this->registered_at <= $this->event->registration_deadline : true);
    }

    /**
     * Confirm the registration
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Approve the registration
     */
    public function approve($approvedBy = null)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'status' => 'confirmed', // Auto-confirm when approved
        ]);
    }

    /**
     * Reject the registration
     */
    public function reject($approvedBy = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Cancel the registration
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Mark as attended
     */
    public function markAttended()
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now(),
        ]);
    }
}
