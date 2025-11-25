<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_submission_id',
        'jury_assignment_id',
        'jury_registration_id',
        'originality_score',
        'methodology_score',
        'clarity_score',
        'contribution_score',
        'overall_score',
        'strengths',
        'weaknesses',
        'comments',
        'confidential_comments',
        'recommendation',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'originality_score' => 'decimal:2',
        'methodology_score' => 'decimal:2',
        'clarity_score' => 'decimal:2',
        'contribution_score' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the paper submission
     */
    public function paperSubmission(): BelongsTo
    {
        return $this->belongsTo(PaperSubmission::class);
    }

    /**
     * Get the jury assignment
     */
    public function juryAssignment(): BelongsTo
    {
        return $this->belongsTo(JuryAssignment::class);
    }

    /**
     * Get the jury member (registration)
     */
    public function juryRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'jury_registration_id');
    }

    /**
     * Calculate overall score from individual scores
     */
    public function calculateOverallScore(): void
    {
        $scores = [
            $this->originality_score,
            $this->methodology_score,
            $this->clarity_score,
            $this->contribution_score,
        ];

        $validScores = array_filter($scores, fn($score) => $score !== null);
        
        if (count($validScores) > 0) {
            $this->overall_score = array_sum($validScores) / count($validScores);
        }
    }

    /**
     * Submit the review
     */
    public function submit(): void
    {
        $this->calculateOverallScore();
        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->save();

        // Update paper's average score
        $this->paperSubmission->updateAverageScore();

        // Mark assignment as completed
        $this->juryAssignment->complete();
    }

    /**
     * Check if review is complete
     */
    public function isComplete(): bool
    {
        return $this->originality_score !== null
            && $this->methodology_score !== null
            && $this->clarity_score !== null
            && $this->contribution_score !== null
            && $this->recommendation !== null;
    }

    /**
     * Scope for submitted reviews
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }
}
