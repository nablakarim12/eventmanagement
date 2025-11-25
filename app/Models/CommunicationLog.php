<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    protected $fillable = [
        'organizer_id',
        'event_id',
        'subject',
        'message',
        'recipient_type',
        'recipients_count',
        'status',
        'scheduled_at',
        'sent_at',
        'type'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    /**
     * Get the organizer that owns the communication
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'organizer_id');
    }

    /**
     * Get the event associated with the communication
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}