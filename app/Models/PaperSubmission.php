<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaperSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_id',
        'submission_code',
        'title',
        'abstract',
        'keywords',
        'paper_file_path',
        'paper_file_name',
        'file_size',
        'status',
        'rejection_reason',
        'average_score',
        'review_count',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'average_score' => 'decimal:2',
        'review_count' => 'integer',
        'file_size' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            if (empty($submission->submission_code)) {
                $submission->submission_code = 'PAPER-' . Str::upper(Str::random(12));
            }
            if (empty($submission->submitted_at)) {
                $submission->submitted_at = now();
            }
        });
    }

    /**
     * Get the event this paper was submitted to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who submitted the paper
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the registration associated with this submission
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }

    /**
     * Get all authors for this paper
     */
    public function authors(): HasMany
    {
        return $this->hasMany(PaperAuthor::class)->orderBy('order');
    }

    /**
     * Get all jury assignments for this paper
     */
    public function juryAssignments(): HasMany
    {
        return $this->hasMany(JuryAssignment::class);
    }

    /**
     * Get all reviews for this paper
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(PaperReview::class);
    }

    /**
     * Calculate and update average score
     */
    public function updateAverageScore(): void
    {
        $reviews = $this->reviews()->where('status', 'submitted')->get();
        
        if ($reviews->count() > 0) {
            $this->average_score = $reviews->avg('overall_score');
            $this->review_count = $reviews->count();
            $this->save();
        }
    }

    /**
     * Check if paper can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return in_array($this->status, ['submitted', 'under_review']);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by event
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
}
