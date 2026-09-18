<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutContent extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'about_contents';

    protected $fillable = [
        'type',
        'title',
        'content',
    ];

    /**
     * Default mission and vision content shown on the public website
     * when no matching record exists in the database.
     *
     * @return array{mission: array{title: string, content: string}, vision: array{title: string, content: string}}
     */
    public static function defaults(): array
    {
        return [
            'mission' => [
                'title' => 'আমাদের মিশন',
                'content' => 'মানসম্মত শিক্ষা প্রদানের মাধ্যমে প্রতিটি শিশুর সর্বোত্তম বিকাশ নিশ্চিত করা। আমরা শিক্ষার মাধ্যমে জ্ঞান, নৈতিকতা ও মূল্যবোধের সমন্বয়ে একটি আলোকিত প্রজন্ম গড়ে তুলতে চাই।',
            ],
            'vision' => [
                'title' => 'আমাদের ভিশন',
                'content' => 'বাংলাদেশের শীর্ষস্থানীয় শিক্ষাপ্রতিষ্ঠান হিসেবে প্রতিষ্ঠিত হওয়া এবং আন্তর্জাতিক মানের শিক্ষা প্রদান করে ছাত্রদের বিশ্ব পর্যায়ে প্রতিযোগী করে গড়ে তোলা।',
            ],
        ];
    }
}
