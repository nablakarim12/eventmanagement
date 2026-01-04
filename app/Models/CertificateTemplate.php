<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'template_type',
        'template_url',
        'cloudinary_public_id',
        'name_x',
        'name_y',
        'name_font_size',
        'name_color',
        'role_x',
        'role_y',
        'role_font_size',
        'role_color',
        'event_name_x',
        'event_name_y',
        'event_name_font_size',
        'event_name_color',
        'date_x',
        'date_y',
        'date_font_size',
        'date_color',
        'font_path',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function generatedCertificates()
    {
        return $this->hasMany(GeneratedCertificate::class, 'template_id');
    }
}
