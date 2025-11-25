<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EventOrganizer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'org_name',
        'org_email',
        'password',
        'description',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'contact_person_name',
        'contact_person_position',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'approved_at'
    ];

    public function documents()
    {
        return $this->hasMany(OrganizerDocument::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
