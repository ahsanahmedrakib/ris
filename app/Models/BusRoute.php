<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    use HasFactory;

    protected $table = 'bus_routes';

    protected $fillable = [
        'bus_id',
        'stop_name',
        'stop_time',
        'stop_order',
    ];

    protected function casts(): array
    {
        return [
            'stop_time' => 'datetime:H:i',
        ];
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
