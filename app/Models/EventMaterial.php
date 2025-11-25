<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'access_type',
        'is_downloadable',
        'available_from',
        'available_until',
        'download_count',
        'is_active'
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'is_downloadable' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer'
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Accessors
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('organizer.materials.download', $this->id);
    }

    public function getIsAvailableAttribute(): bool
    {
        $now = now();
        
        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }
        
        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }
        
        return $this->is_active;
    }

    // Methods
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function canBeAccessedBy($user, $registration = null): bool
    {
        if (!$this->is_available) {
            return false;
        }

        switch ($this->access_type) {
            case 'public':
                return true;
            case 'registered_only':
                return $registration && $registration->user_id === $user->id;
            case 'checked_in_only':
                $attendance = EventAttendance::where('event_id', $this->event_id)
                    ->where('user_id', $user->id)
                    ->whereNotNull('check_in_time')
                    ->first();
                return $attendance !== null;
            default:
                return false;
        }
    }
}
