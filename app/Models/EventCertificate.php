<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'attendance_id',
        'certificate_number',
        'participant_name',
        'event_title',
        'event_date',
        'attendance_hours',
        'template_used',
        'certificate_path',
        'generated_at',
        'emailed_at',
        'download_count',
        'last_downloaded_at',
        'is_verified',
        'verification_code',
        'certificate_data'
    ];

    protected $casts = [
        'event_date' => 'date',
        'generated_at' => 'datetime',
        'emailed_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
        'is_verified' => 'boolean',
        'download_count' => 'integer',
        'attendance_hours' => 'integer',
        'certificate_data' => 'json'
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(EventAttendance::class, 'attendance_id');
    }

    // Accessors
    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->verification_code);
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->verification_code);
    }

    public function getCertificateUrlAttribute(): string
    {
        return asset('storage/' . $this->certificate_path);
    }

    // Methods
    public static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (self::where('certificate_number', $number)->exists());
        
        return $number;
    }

    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (self::where('verification_code', $code)->exists());
        
        return $code;
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    public function markAsEmailed(): void
    {
        $this->update(['emailed_at' => now()]);
    }

    public static function createFromAttendance(EventAttendance $attendance): self
    {
        $event = $attendance->event;
        $user = $attendance->user;

        return self::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'certificate_number' => self::generateCertificateNumber(),
            'participant_name' => $user->name,
            'event_title' => $event->title,
            'event_date' => $event->event_date,
            'attendance_hours' => $attendance->duration_hours,
            'template_used' => 'default',
            'generated_at' => now(),
            'verification_code' => self::generateVerificationCode(),
            'certificate_data' => [
                'organizer' => $event->organizer->org_name,
                'location' => $event->location,
                'description' => $event->description,
                'attendance_duration' => $attendance->formatted_duration
            ]
        ]);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
