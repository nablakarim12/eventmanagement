<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'description',
        'short_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue_name',
        'venue_address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'max_participants',
        'current_participants',
        'registration_fee',
        'is_free',
        'registration_deadline',
        'status',
        'requires_approval',
        'is_public',
        'allow_waitlist',
        'requirements',
        'tags',
        'contact_email',
        'contact_phone',
        'website_url',
        'slug',
        'featured_image',
        'gallery_images',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'registration_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_free' => 'boolean',
        'requires_approval' => 'boolean',
        'is_public' => 'boolean',
        'allow_waitlist' => 'boolean',
        'requirements' => 'array',
        'tags' => 'array',
        'gallery_images' => 'array',
    ];

    // Boolean mutators for PostgreSQL compatibility
    public function setIsFreeAttribute($value)
    {
        $this->attributes['is_free'] = $value ? 'true' : 'false';
    }

    public function setRequiresApprovalAttribute($value)
    {
        $this->attributes['requires_approval'] = $value ? 'true' : 'false';
    }

    public function setIsPublicAttribute($value)
    {
        $this->attributes['is_public'] = $value ? 'true' : 'false';
    }

    public function setAllowWaitlistAttribute($value)
    {
        $this->attributes['allow_waitlist'] = $value ? 'true' : 'false';
    }

    // Relationships
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    // public function registrations(): HasMany
    // {
    //     return $this->hasMany(EventRegistration::class);
    // }

    // Accessors
    public function getIsFullAttribute(): bool
    {
        return $this->max_participants && $this->current_participants >= $this->max_participants;
    }

    public function getAvailableSlotsAttribute(): int
    {
        if (!$this->max_participants) {
            return PHP_INT_MAX;
        }
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getCanRegisterAttribute(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        if ($this->is_full && !$this->allow_waitlist) {
            return false;
        }

        return true;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title . '-' . Str::random(6));
            }
        });
    }
}
