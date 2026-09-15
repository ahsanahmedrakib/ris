<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTransport extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'student_transport';

    protected $fillable = [
        'student_id',
        'bus_id',
        'route_id',
        'pickup_stop',
        'dropoff_stop',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function route()
    {
        return $this->belongsTo(BusRoute::class, 'route_id');
    }
}
