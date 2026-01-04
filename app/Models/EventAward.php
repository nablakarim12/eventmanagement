<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'display_name',
        'rank',
        'award_type',
        'is_mandatory',
        'description',
        'color',
        'order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'rank' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the event that owns the award
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get all participant awards for this award
     */
    public function participantAwards()
    {
        return $this->hasMany(ParticipantAward::class);
    }

    /**
     * Scope to get mandatory awards (Gold, Silver, Bronze)
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope to get custom awards
     */
    public function scopeCustom($query)
    {
        return $query->where('is_mandatory', false);
    }
}
