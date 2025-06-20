<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['name', 'user_id', 'vehicle_id', 'driver_id', 'region_id', 'start_date', 'end_date', 'status', 'total_price'];

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
