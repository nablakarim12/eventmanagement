<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_submission_id',
        'name',
        'email',
        'affiliation',
        'country',
        'is_corresponding',
        'order',
    ];

    protected $casts = [
        'is_corresponding' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the paper submission this author belongs to
     */
    public function paperSubmission(): BelongsTo
    {
        return $this->belongsTo(PaperSubmission::class);
    }
}
