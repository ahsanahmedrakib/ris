<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreValue extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'core_values';

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Default core values shown on the public website when none exist
     * in the database.
     *
     * @return array<int, array{title: string, description: string}>
     */
    public static function defaults(): array
    {
        return [
            [
                'title' => 'মানসম্মত শিক্ষা',
                'description' => 'প্রতিটি শিশুর জন্য সর্বোত্তম শিক্ষা অভিজ্ঞতা নিশ্চিত করা।',
            ],
            [
                'title' => 'নৈতিকতা',
                'description' => 'সততা, সম্মান ও দায়িত্বশীলতার শিক্ষা প্রদান।',
            ],
            [
                'title' => 'সৃজনশীলতা',
                'description' => 'প্রতিটি শিশুর সৃজনশীল ক্ষমতাকে উৎসাহিত ও বিকশিত করা।',
            ],
            [
                'title' => 'সম্প্রীতি',
                'description' => 'শিক্ষক, ছাত্র ও অভিভাবকদের মধ্যে সৌজন্য ও সম্প্রীতির বন্ধন।',
            ],
        ];
    }
}
