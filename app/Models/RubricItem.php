<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_category_id',
        'name',
        'description',
        'max_score',
        'order',
        'is_active',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RubricCategory::class, 'rubric_category_id');
    }

    /**
     * Get all score level descriptions
     */
    public function scoreLevels(): HasMany
    {
        return $this->hasMany(RubricScoreLevel::class)->orderBy('level');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
