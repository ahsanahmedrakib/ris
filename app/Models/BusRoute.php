<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusRoute extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

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
