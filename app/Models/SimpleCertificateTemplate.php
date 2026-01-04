<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimpleCertificateTemplate extends Model
{
    protected $fillable = [
        'event_id',
        'certificate_type',
        'participant_cert_type',
        'use_custom_template',
        'custom_template_url',
        'custom_template_cloudinary_id',
        'text_positions',
        'use_ai_generation',
        'template_file_url',
        'template_file_type',
        'template_cloudinary_id',
        'generation_prompt',
        'template_design',
        'primary_color',
        'secondary_color',
        'text_color',
        'logo_url',
        'logo_cloudinary_id',
        'signature_url',
        'signature_cloudinary_id',
        'signature_name',
        'signature_title',
    ];

    protected $casts = [
        'text_positions' => 'array',
        'use_custom_template' => 'boolean',
        'use_ai_generation' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
