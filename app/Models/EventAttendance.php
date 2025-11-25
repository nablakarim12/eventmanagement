<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class EventAttendance extends Model
{
    use HasFactory;

    protected $table = 'event_attendance';

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_id',
        'check_in_time',
        'check_out_time',
        'check_in_method',
        'check_out_method',
        'check_in_location',
        'check_out_location',
        'total_duration_minutes',
        'status',
        'notes',
        'certificate_generated',
        'certificate_generated_at'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'certificate_generated' => 'boolean',
        'certificate_generated_at' => 'datetime',
        'total_duration_minutes' => 'integer'
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

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(EventCertificate::class, 'attendance_id');
    }

    // Accessors
    public function getIsCheckedInAttribute(): bool
    {
        return $this->check_in_time !== null;
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return $this->check_out_time !== null;
    }

    public function getDurationHoursAttribute(): ?float
    {
        if ($this->total_duration_minutes) {
            return round($this->total_duration_minutes / 60, 2);
        }
        return null;
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->total_duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->total_duration_minutes / 60);
        $minutes = $this->total_duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    // Methods
    public function checkIn(string $method = 'qr', ?string $location = null): void
    {
        $this->update([
            'check_in_time' => now(),
            'check_in_method' => $method,
            'check_in_location' => $location,
            'status' => 'present'
        ]);
    }

    public function checkOut(string $method = 'qr', ?string $location = null): void
    {
        if (!$this->check_in_time) {
            throw new \Exception('Cannot check out without checking in first');
        }

        $checkOutTime = now();
        $duration = $this->check_in_time->diffInMinutes($checkOutTime);

        $this->update([
            'check_out_time' => $checkOutTime,
            'check_out_method' => $method,
            'check_out_location' => $location,
            'total_duration_minutes' => $duration
        ]);

        // Check if certificate should be generated
        $this->checkForCertificateGeneration();
    }

    public function calculateDuration(): void
    {
        if ($this->check_in_time && $this->check_out_time) {
            $this->update([
                'total_duration_minutes' => $this->check_in_time->diffInMinutes($this->check_out_time)
            ]);
        }
    }

    protected function checkForCertificateGeneration(): void
    {
        // Auto-generate certificate if attendance meets criteria
        if (!$this->certificate_generated && $this->shouldGenerateCertificate()) {
            // This will be implemented when we create the certificate service
            dispatch(new \App\Jobs\GenerateCertificate($this));
        }
    }

    protected function shouldGenerateCertificate(): bool
    {
        // Criteria for certificate generation
        $event = $this->event;
        
        // Check if event has minimum attendance requirement
        $minAttendanceHours = $event->min_attendance_hours ?? 1; // Default 1 hour
        
        return $this->duration_hours >= $minAttendanceHours;
    }

    // Scopes
    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('check_in_time');
    }

    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('check_out_time');
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }
}
