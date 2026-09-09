<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_no',
        'driver_name',
        'driver_phone',
        'capacity',
        'route_name',
    ];

    public function busRoutes()
    {
        return $this->hasMany(BusRoute::class);
    }

    public function studentTransports()
    {
        return $this->hasMany(StudentTransport::class);
    }
}
