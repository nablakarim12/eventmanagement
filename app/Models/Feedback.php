<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'event_registration_id',
        'overall_rating',
        'content_rating',
        'organization_rating',
        'platform_rating',
        'venue_rating',
        'comments',
        'suggestions',
        'system_feedback',
        'would_recommend',
        'submitted_at'
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the event registration that owns the feedback
     */
    public function eventRegistration()
    {
        return $this->belongsTo(EventRegistration::class);
    }
}
