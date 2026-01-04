<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_id',
        'template_id',
        'certificate_url',
        'cloudinary_public_id',
        'certificate_type',
        'participant_name',
        'project_name',
        'participant_role',
        'event_name',
        'event_date',
        'generated_at',
        'downloaded_at',
        'emailed_at',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'generated_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }
}
