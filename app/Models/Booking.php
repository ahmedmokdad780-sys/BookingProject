<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'status',
        'total_price',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

}
