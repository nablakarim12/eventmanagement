<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventOrganizerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_organizer_id',
        'document_type',
        'file_name',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'notes'
    ];

    public function eventOrganizer()
    {
        return $this->belongsTo(EventOrganizer::class);
    }
}
