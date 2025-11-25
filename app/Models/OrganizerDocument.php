<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_organizer_id',
        'file_path',
        'original_name',
        'document_type'
    ];

    public function organizer()
    {
        return $this->belongsTo(EventOrganizer::class, 'event_organizer_id');
    }
}
