<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationTemplate extends Model
{
    protected $fillable = [
        'organizer_id',
        'name',
        'subject',
        'message',
        'type'
    ];

    /**
     * Get the organizer that owns the template
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'organizer_id');
    }
}