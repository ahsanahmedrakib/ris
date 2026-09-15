<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroSlide extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'hero_slides';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'btn_text',
        'link',
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

    public static function defaults(): array
    {
        return [
            [
                'image' => 'images/home/hero-1.jpg',
                'title' => 'শিক্ষাই আলোকিত ভবিষ্যতের পথ',
                'subtitle' => 'রেশমা ইন্টারন্যাশনাল স্কুলে আপনার সন্তানের জন্য সেরা শিক্ষা অভিজ্ঞতা।',
                'btn_text' => 'ভর্তি করুন',
                'link' => 'admission',
            ],
            [
                'image' => 'images/home/hero-2.jpg',
                'title' => 'আধুনিক শিক্ষা পদ্ধতি',
                'subtitle' => 'ডিজিটাল ক্লাসরুম ও ইন্টারঅ্যাক্টিভ লার্নিংয়ের মাধ্যমে শিক্ষা।',
                'btn_text' => 'আমাদের সম্পর্কে',
                'link' => 'about',
            ],
            [
                'image' => 'images/home/hero-3.jpg',
                'title' => 'নিরাপদ ও আরামদায়ক পরিবেশ',
                'subtitle' => 'প্রতিটি শিশুর নিরাপত্তা ও সুস্বাস্থ্য আমাদের অগ্রাধিকার।',
                'btn_text' => 'যোগাযোগ করুন',
                'link' => 'contact',
            ],
            [
                'image' => 'images/home/hero-4.jpg',
                'title' => 'মেধাবৃত্তি ও বৃত্তি সুবিধা',
                'subtitle' => 'প্রতিভাবান শিক্ষার্থীদের জন্য বিশেষ মেধাবৃত্তি কার্যক্রম।',
                'btn_text' => 'মেধাবৃত্তি',
                'link' => 'scholarship',
            ],
            [
                'image' => 'images/home/hero-5.jpg',
                'title' => 'অভিজ্ঞ ও প্রশিক্ষিত শিক্ষকমণ্ডলী',
                'subtitle' => 'প্রতিটি ক্লাসে অভিজ্ঞ শিক্ষকদের আন্তরিক তত্ত্বাবধানে সেরা শিক্ষা।',
                'btn_text' => 'শিক্ষকদের দেখুন',
                'link' => 'teachers',
            ],
            [
                'image' => 'images/home/hero-6.jpg',
                'title' => 'সুবিশাল ক্যাম্পাস ও আধুনিক সুবিধা',
                'subtitle' => 'বিজ্ঞানাগার, লাইব্রেরি ও ডিজিটাল ক্লাসরুমসহ আধুনিক ক্যাম্পাস।',
                'btn_text' => 'গ্যালারি দেখুন',
                'link' => 'gallery',
            ],
            [
                'image' => 'images/home/hero-7.jpg',
                'title' => 'সাংস্কৃতিক ও সহশিক্ষা কার্যক্রম',
                'subtitle' => 'খেলাধুলা, সাংস্কৃতিক অনুষ্ঠান ও নানা প্রতিযোগিতায় শিক্ষার্থীদের উৎসাহ।',
                'btn_text' => 'আমাদের সম্পর্কে',
                'link' => 'about',
            ],
            [
                'image' => 'images/home/hero-8.jpg',
                'title' => 'বিজ্ঞান ও প্রযুক্তিতে বিশেষ নজর',
                'subtitle' => 'আধুনিক বিজ্ঞানাগার ও প্রযুক্তিনির্ভর শিক্ষায় ভবিষ্যৎ প্রজন্মের প্রস্তুতি।',
                'btn_text' => 'যোগাযোগ করুন',
                'link' => 'contact',
            ],
            [
                'image' => 'images/home/hero-9.jpg',
                'title' => 'নিরাপদ ও আনন্দময় ক্যাম্পাস লাইফ',
                'subtitle' => 'শিক্ষার্থীদের নিরাপত্তায় সার্বক্ষণিক নজরদারি ও সুশৃঙ্খল পরিবেশ।',
                'btn_text' => 'আমাদের সম্পর্কে',
                'link' => 'about',
            ],
            [
                'image' => 'images/home/hero-10.jpg',
                'title' => 'প্রিয় ক্যাম্পাসের স্মরণীয় মুহূর্ত',
                'subtitle' => 'শিক্ষার্থীদের আনন্দময় ক্যাম্পাস ্লাইফের ছবি দেখে নিন।',
                'btn_text' => 'গ্যালারি দেখুন',
                'link' => 'gallery',
            ],
        ];
    }
}
