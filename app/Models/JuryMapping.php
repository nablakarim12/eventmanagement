<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuryMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'jury_registration_id',
        'participant_registration_id',
        'status',
        'notes',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function reviewerRegistration()
    {
        return $this->belongsTo(EventRegistration::class, 'jury_registration_id');
    }

    public function participantRegistration()
    {
        return $this->belongsTo(EventRegistration::class, 'participant_registration_id');
    }
}
