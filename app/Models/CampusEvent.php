<?php

namespace App\Models;

use App\Core\Traits\HasSlug;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusEvent extends Model
{
    use HasFactory, HasSlug, LogsActivity, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'date',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function slugPrefix(): string
    {
        return 'campus-event';
    }
}
