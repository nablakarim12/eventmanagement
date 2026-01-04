<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_award_id',
        'user_id',
        'registration_id',
        'final_score',
        'category',
        'theme',
        'ranking_scope',
        'rank',
        'is_published',
        'organizer_notes',
    ];

    protected $casts = [
        'final_score' => 'decimal:2',
        'rank' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the award
     */
    public function award()
    {
        return $this->belongsTo(EventAward::class, 'event_award_id');
    }

    /**
     * Get the user/participant
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the registration
     */
    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }

    /**
     * Scope to get published awards
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to filter by ranking scope
     */
    public function scopeByRankingScope($query, $scope)
    {
        return $query->where('ranking_scope', $scope);
    }
}
