<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubricScoreLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_item_id',
        'level',
        'description',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get the rubric item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(RubricItem::class, 'rubric_item_id');
    }
}
