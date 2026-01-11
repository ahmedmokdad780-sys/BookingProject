<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'governorate',
        'city',
        'location',
        'type',
        'rooms',
        'bathrooms',
        'area',
        'price',
        'description',
    ];
    public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'apartment_id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected $appends = ['is_favorited'];

    public function getIsFavoritedAttribute()
    {
        $userId = auth('sanctum')->id();

        if ($userId) {
            return $this->favoritedBy()
                ->where('user_id', $userId)
                ->exists();
        }

        return false;
    }
}
