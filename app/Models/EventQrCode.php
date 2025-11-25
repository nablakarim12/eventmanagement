<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'qr_code',
        'type',
        'qr_image_path',
        'data',
        'valid_from',
        'valid_until',
        'is_active',
        'scan_count',
        'last_scanned_at',
        'description'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'scan_count' => 'integer',
        'last_scanned_at' => 'datetime',
        'data' => 'json'
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Accessors
    public function getIsValidAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }
        
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }
        
        return true;
    }

    public function getQrImageUrlAttribute(): string
    {
        return asset('storage/' . $this->qr_image_path);
    }

    // Methods
    public function incrementScanCount(): void
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    /**
     * Generate QR codes for eligible users only
     */
    public static function generateForEligibleUsers(Event $event): array
    {
        $generated = [];
        
        // Get eligible users (those who meet registration requirements)
        $eligibleRegistrations = EventRegistration::where('event_id', $event->id)
            ->where(function($query) {
                $query->where(function($q) {
                    // Participants who submitted materials
                    $q->where('role_type', 'participant')
                      ->where('materials_submitted', true);
                })->orWhere(function($q) {
                    // Approved jury members
                    $q->where('role_type', 'jury')
                      ->where('approval_status', 'approved');
                })->orWhere(function($q) {
                    // Both role: must have materials AND be approved
                    $q->where('role_type', 'both')
                      ->where('materials_submitted', true)
                      ->where('approval_status', 'approved');
                });
            })
            ->with('user')
            ->get();

        // Check registration deadline
        if ($event->registration_deadline && now() > $event->registration_deadline) {
            throw new \Exception('Registration deadline has passed. Cannot generate QR codes.');
        }

        foreach ($eligibleRegistrations as $registration) {
            // Check if QR code already exists for this user
            $existingQr = self::where('event_id', $event->id)
                             ->where('user_id', $registration->user_id)
                             ->first();

            if (!$existingQr) {
                // Generate QR code for eligible user
                $qrCode = self::generateForUser($event, $registration->user, $registration->role_type);
                $generated[] = $qrCode;
            }
        }

        return $generated;
    }

    /**
     * Generate QR code for specific user with role
     */
    public static function generateForUser(Event $event, $user, string $roleType): self
    {
        // Generate unique QR code
        $qrCode = Str::uuid()->toString();
        
        // Store relevant data in the QR code
        $data = [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'role_type' => $roleType,
            'type' => 'attendance',
            'generated_at' => now()->toISOString()
        ];

        // Generate image file path
        $fileName = "qr_attendance_{$event->id}_{$user->id}_" . time() . ".png";
        $imagePath = "qr_codes/{$fileName}";

        return self::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'qr_code' => $qrCode,
            'type' => 'attendance',
            'data' => $data,
            'qr_image_path' => $imagePath,
            'valid_from' => $event->start_date,
            'valid_until' => $event->end_date ?? $event->start_date->addDay(),
            'description' => "Attendance QR for {$user->name} ({$roleType}) - {$event->title}"
        ]);
    }

    public static function generateForEvent(Event $event, string $type = 'check_in'): self
    {
        // Generate unique QR code
        $qrCode = Str::uuid()->toString();
        
        // Store relevant data in the QR code
        $data = [
            'event_id' => $event->id,
            'type' => $type,
            'generated_at' => now()->toISOString()
        ];

        // Generate image file path
        $fileName = "qr_code_{$event->id}_{$type}_" . time() . ".png";
        $imagePath = "qr_codes/{$fileName}";

        return self::create([
            'event_id' => $event->id,
            'qr_code' => $qrCode,
            'type' => $type,
            'data' => $data,
            'qr_image_path' => $imagePath,
            'valid_from' => $event->start_date,
            'valid_until' => $event->end_date ?? $event->start_date->addDay(),
            'description' => "QR Code for {$type} - {$event->title}"
        ]);
    }

    public function generateQrImage(): void
    {
        // This method will generate the actual QR code image
        // We'll implement this with a QR code library
        $qrData = json_encode([
            'qr_code' => $this->qr_code,
            'event_id' => $this->event_id,
            'type' => $this->type
        ]);

        // For now, we'll create a placeholder path
        // In a real implementation, you'd use a QR code library like endroid/qr-code
        $fileName = "qr_code_{$this->id}_{$this->type}.png";
        $path = "qr_codes/{$fileName}";
        
        $this->update(['qr_image_path' => $path]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Instance methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        // Check valid_from
        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }
        
        // Check valid_until
        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }
        
        return true;
    }
}
