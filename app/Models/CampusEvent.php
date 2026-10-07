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
        'video',
        'images',
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
            'images' => 'array',
        ];
    }

    /**
     * Gallery paths for the single event view, always in card-image order.
     *
     * Rows written before the gallery column existed carry only the cover, so
     * they fall back to it rather than rendering an empty page.
     *
     * @return list<string>
     */
    public function galleryImages(): array
    {
        return array_values(array_filter($this->images ?? []))
            ?: array_values(array_filter([$this->image]));
    }

    protected function slugPrefix(): string
    {
        return 'campus-event';
    }
}
