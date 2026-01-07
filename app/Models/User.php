<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'password',
        'birthdate',
        'account_type',
        'status',
        'national_id_image',
        'personal_image',
        'is_active',
        'approved_at',
        'approved_by'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birthdate' => 'date',
        'approved_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }
    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }
    public function scopeRejected($q)
    {
        return $q->where('status', 'rejected');
    }

    public function isAdmin()
    {
        return $this->account_type === 'admin';
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function favorites()
    {
        return $this->belongsToMany(Apartment::class, 'favorites')
            ->withTimestamps();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
