<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'delivery_mode',
        'innovation_categories',
        'innovation_theme',
        'conference_categories',
        'title',
        'description',
        'short_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        // Face-to-face specific fields
        'f2f_start_date',
        'f2f_end_date',
        'f2f_start_time',
        'f2f_end_time',
        'f2f_registration_deadline',
        'f2f_paper_deadline',
        'f2f_acceptance_date',
        'f2f_jury_registration_deadline',
        'f2f_payment_deadline',
        'f2f_extended_paper_deadlines',
        'f2f_extended_acceptance_dates',
        'f2f_extension_count',
        // New innovation date fields
        'jury_registration_deadline',
        'submission_deadline',
        'acceptance_notification_date',
        'extended_registration_deadline',
        'extended_jury_deadline',
        'extended_submission_deadline',
        'extended_notification_date',
        'f2f_submission_deadline',
        'f2f_acceptance_notification_date',
        'f2f_extended_registration_deadline',
        'f2f_extended_jury_deadline',
        'f2f_extended_submission_deadline',
        'f2f_extended_notification_date',
        'f2f_payment_deadline_new',
        // Online specific fields
        'online_start_date',
        'online_end_date',
        'online_start_time',
        'online_end_time',
        'online_platform_url',
        'online_registration_deadline',
        'online_paper_deadline',
        'online_acceptance_date',
        'online_jury_registration_deadline',
        'online_extended_paper_deadlines',
        'online_extended_acceptance_dates',
        'online_extension_count',
        'online_jury_registration_deadline_new',
        'online_submission_deadline',
        'online_acceptance_notification_date_new',
        'online_extended_registration_deadline',
        'online_extended_jury_deadline',
        'online_extended_submission_deadline',
        'online_extended_notification_date',
        'online_payment_deadline_new',
        // Conference-specific fields
        'conference_categories',
        'reviewer_registration_deadline',
        'paper_submission_deadline',
        'review_deadline',
        'acceptance_notification_date',
        'f2f_reviewer_registration_deadline',
        'f2f_paper_submission_deadline',
        'f2f_review_deadline',
        'f2f_acceptance_notification_date',
        'f2f_payment_deadline',
        'online_reviewer_registration_deadline',
        'online_paper_submission_deadline',
        'online_review_deadline',
        'online_acceptance_notification_date',
        'online_payment_deadline',
        'min_abstract_words',
        'min_keywords',
        'max_paper_size_mb',
        'paper_format_guidelines',
        'allow_multiple_submissions',
        'min_reviewers_per_paper',
        // Common fields
        'venue_name',
        'venue_address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'max_participants',
        'current_participants',
        'registration_fee',
        'payment_deadline',
        'is_free',
        'registration_deadline',
        'status',
        'requires_approval',
        'is_public',
        'allow_waitlist',
        'requirements',
        'tags',
        'contact_email',
        'contact_phone',
        'website_url',
        'slug',
        'featured_image',
        'featured_image_public_id',
        'gallery_images',
        'views',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        // Face-to-face dates
        'f2f_start_date' => 'datetime',
        'f2f_end_date' => 'datetime',
        'f2f_start_time' => 'datetime:H:i',
        'f2f_end_time' => 'datetime:H:i',
        'f2f_registration_deadline' => 'datetime',
        'f2f_paper_deadline' => 'date',
        'f2f_acceptance_date' => 'date',
        'f2f_jury_registration_deadline' => 'datetime',
        'f2f_extended_paper_deadlines' => 'array',
        'f2f_extended_acceptance_dates' => 'array',
        // New innovation date casts
        'jury_registration_deadline' => 'datetime',
        'submission_deadline' => 'datetime',
        'acceptance_notification_date' => 'datetime',
        'extended_registration_deadline' => 'datetime',
        'extended_jury_deadline' => 'datetime',
        'extended_submission_deadline' => 'datetime',
        'extended_notification_date' => 'datetime',
        'f2f_submission_deadline' => 'datetime',
        'f2f_acceptance_notification_date' => 'datetime',
        'online_jury_registration_deadline_new' => 'datetime',
        'online_submission_deadline' => 'datetime',
        'online_acceptance_notification_date_new' => 'datetime',
        'online_extended_registration_deadline' => 'datetime',
        'online_extended_jury_deadline' => 'datetime',
        'online_extended_submission_deadline' => 'datetime',
        'online_extended_notification_date' => 'datetime',
        'online_payment_deadline_new' => 'datetime',
        'f2f_extended_registration_deadline' => 'datetime',
        'f2f_extended_jury_deadline' => 'datetime',
        'f2f_extended_submission_deadline' => 'datetime',
        'f2f_extended_notification_date' => 'datetime',
        'f2f_payment_deadline_new' => 'datetime',
        // Online dates
        'online_start_date' => 'datetime',
        'online_end_date' => 'datetime',
        'online_start_time' => 'datetime:H:i',
        'online_end_time' => 'datetime:H:i',
        'online_paper_deadline' => 'date',
        'online_acceptance_date' => 'date',
        'online_jury_registration_deadline' => 'datetime',
        'online_extended_paper_deadlines' => 'array',
        'online_extended_acceptance_dates' => 'array',
        // Conference-specific dates
        'conference_categories' => 'array',
        'innovation_categories' => 'array',
        'innovation_theme' => 'array',
        'reviewer_registration_deadline' => 'datetime',
        'paper_submission_deadline' => 'datetime',
        'review_deadline' => 'datetime',
        'acceptance_notification_date' => 'datetime',
        'f2f_reviewer_registration_deadline' => 'datetime',
        'f2f_paper_submission_deadline' => 'datetime',
        'f2f_review_deadline' => 'datetime',
        'f2f_acceptance_notification_date' => 'datetime',
        'f2f_payment_deadline' => 'datetime',
        'online_reviewer_registration_deadline' => 'datetime',
        'online_paper_submission_deadline' => 'datetime',
        'online_review_deadline' => 'datetime',
        'online_acceptance_notification_date' => 'datetime',
        'online_payment_deadline' => 'datetime',
        'payment_deadline' => 'datetime',
        // Other fields
        'registration_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_free' => 'boolean',
        'requires_approval' => 'boolean',
        'is_public' => 'boolean',
        'allow_waitlist' => 'boolean',
        'requirements' => 'array',
        'innovation_categories' => 'array',
        'conference_categories' => 'array',
        'tags' => 'array',
        'gallery_images' => 'array',
    ];

    // Boolean mutators for PostgreSQL compatibility
    public function setIsFreeAttribute($value)
    {
        $this->attributes['is_free'] = $value ? 'true' : 'false';
    }

    public function setRequiresApprovalAttribute($value)
    {
        $this->attributes['requires_approval'] = $value ? 'true' : 'false';
    }

    public function setIsPublicAttribute($value)
    {
        $this->attributes['is_public'] = $value ? 'true' : 'false';
    }

    public function setAllowWaitlistAttribute($value)
    {
        $this->attributes['allow_waitlist'] = $value ? 'true' : 'false';
    }

    // Relationships
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(EventMaterial::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function certificateTemplates(): HasMany
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function simpleCertificateTemplates(): HasMany
    {
        return $this->hasMany(SimpleCertificateTemplate::class);
    }

    public function generatedCertificates(): HasMany
    {
        return $this->hasMany(GeneratedCertificate::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(EventQrCode::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(EventCertificate::class);
    }

    public function paperSubmissions(): HasMany
    {
        return $this->hasMany(PaperSubmission::class);
    }

    public function reviewCriteria(): HasMany
    {
        return $this->hasMany(ReviewCriteria::class);
    }

    public function rubricCategories(): HasMany
    {
        return $this->hasMany(RubricCategory::class)->orderBy('order');
    }

    // Accessors
    public function getIsFullAttribute(): bool
    {
        return $this->max_participants && $this->current_participants >= $this->max_participants;
    }

    public function getAvailableSlotsAttribute(): int
    {
        if (!$this->max_participants) {
            return PHP_INT_MAX;
        }
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getCanRegisterAttribute(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        if ($this->is_full && !$this->allow_waitlist) {
            return false;
        }

        return true;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    // Registration Deadline Helper Methods
    public function isRegistrationOpen(): bool
    {
        // Check if event is published and public
        if ($this->status !== 'published' || !$this->is_public) {
            return false;
        }

        // Check if event has already started
        if ($this->start_date->isPast()) {
            return false;
        }

        // Check registration deadline
        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        // Check capacity
        if ($this->max_participants && $this->current_participants >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function isRegistrationDeadlinePassed(): bool
    {
        return $this->registration_deadline && $this->registration_deadline->isPast();
    }

    public function getRegistrationStatus(): string
    {
        if ($this->start_date->isPast()) {
            return 'event_ended';
        }

        if ($this->isRegistrationDeadlinePassed()) {
            return 'deadline_passed';
        }

        if ($this->max_participants && $this->current_participants >= $this->max_participants) {
            return 'full';
        }

        if ($this->status !== 'published' || !$this->is_public) {
            return 'not_available';
        }

        return 'open';
    }

    public function getDaysUntilRegistrationDeadline(): ?int
    {
        if (!$this->registration_deadline) {
            return null;
        }

        return now()->diffInDays($this->registration_deadline, false);
    }

    public function getHoursUntilRegistrationDeadline(): ?int
    {
        if (!$this->registration_deadline) {
            return null;
        }

        return now()->diffInHours($this->registration_deadline, false);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title . '-' . Str::random(6));
            }
        });

        static::created(function ($event) {
            // Auto-generate QR codes for the event
            \App\Models\EventQrCode::generateForEvent($event, 'check_in');
            \App\Models\EventQrCode::generateForEvent($event, 'check_out');
        });
    }
}
