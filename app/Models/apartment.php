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

    
}
