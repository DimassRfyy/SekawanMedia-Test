<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['name', 'type', 'ownership', 'brand', 'model'];

    public function bookings() {
        return $this->hasMany(Booking::class);
    }
}
