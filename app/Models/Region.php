<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'type'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }
}
