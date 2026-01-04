<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'title',
        'abstract',
        'poster_path',
        'status',
        'paper_category',
        'product_category',
        'product_theme',
    ];

    /**
     * Get the event this paper belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who submitted this paper
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the registration associated with this paper
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'user_id', 'user_id')
            ->where('event_id', $this->event_id);
    }
}
