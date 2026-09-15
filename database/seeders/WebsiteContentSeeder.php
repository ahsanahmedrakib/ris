<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WebsiteContentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedTestimonials();
        $this->seedGallery();
    }

    protected function seedTestimonials(): void
    {
        if (Testimonial::count() > 0) {
            return;
        }

        $testimonials = [
            [
                'name' => 'মোঃ আব্দুল করিম',
                'designation' => 'অভিভাবক',
                'message' => 'রেশমা ইন্টারন্যাশনাল স্কুলে আমার সন্তানের শিক্ষার মান দেখে আমি সত্যিই মুগ্ধ। শিক্ষকরা অত্যন্ত যত্নশীল এবং পড়াশোনার পাশাপাশি শিশুদের নৈতিক শিক্ষাতেও বিশেষ গুরুত্ব দেন।',
                'rating' => 5,
            ],
            [
                'name' => 'সুমাইয়া আক্তার',
                'designation' => 'অভিভাবক',
                'message' => 'স্কুলের পরিকাঠামো, পরিবেশ এবং নিরাপত্তা ব্যবস্থা চমৎকার। আমার মেয়ে স্কুলে যেতে খুব ভালোবাসে। এই স্কুল নির্বাচন করে আমরা সত্যিই সঠিক সিদ্ধান্ত নিয়েছি।',
                'rating' => 5,
            ],
            [
                'name' => 'রাকিবুল হাসান',
                'designation' => 'অভিভাবক',
                'message' => 'শিক্ষকদের সঙ্গে যোগাযোগের সুযোগ ও অভিভাবক সমাবেশগুলো খুবই কার্যকর। স্কুল কর্তৃপক্ষ সবসময় অভিভাবকদের মতামতকে গুরুত্ব দেয়।',
                'rating' => 4,
            ],
            [
                'name' => 'নুসরাত জাহান',
                'designation' => 'প্রাক্তন শিক্ষার্থী',
                'message' => 'এই স্কুলেই আমার পড়াশোনার ভিত তৈরি হয়েছিল। শিক্ষকদের অকৃত্রিম ভালোবাসা ও স্নেহ আজও মনে পড়ে। ফলাফল এবং সুশৃঙ্খল পরিবেশ দুটোই এই স্কুলের অন্যতম সম্পদ।',
                'rating' => 5,
            ],
            [
                'name' => 'মোহাম্মদ শফিকুল ইসলাম',
                'designation' => 'অভিভাবক',
                'message' => 'ডিজিটাল ক্লাসরুম এবং বিজ্ঞানাগারের ব্যবস্থা অসাধারণ। শিশুদের আধুনিক প্রযুক্তিতে শিক্ষা দেওয়ার যে আয়োজন এখানে, তা সত্যিই প্রশংসনীয়।',
                'rating' => 5,
            ],
            [
                'name' => 'ফাতেমা বেগম',
                'designation' => 'অভিভাবক',
                'message' => 'স্কুলের পরিবহন ও নিরাপত্তা ব্যবস্থা নিয়ে কোনো চিন্তা করতে হয় না। শিক্ষার্থীদের প্রতি শিক্ষকদের দায়িত্ববোধ সত্যিই দৃষ্টান্তমূলক।',
                'rating' => 4,
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::create([
                'name' => $testimonial['name'],
                'designation' => $testimonial['designation'],
                'photo' => null,
                'message' => $testimonial['message'],
                'rating' => $testimonial['rating'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }

    protected function seedGallery(): void
    {
        if (GalleryItem::count() > 0) {
            return;
        }

        $items = [
            ['title' => 'বার্ষিক ক্রীড়া প্রতিযোগিতা', 'category' => 'ক্রীড়া', 'labels' => ['বার্ষিক', 'ক্রীড়া']],
            ['title' => 'বিজ্ঞান মেলা ও প্রদর্শনী', 'category' => 'বিজ্ঞান', 'labels' => ['বিজ্ঞান', 'মেলা']],
            ['title' => 'নতুন শিক্ষাবর্ষের প্রথম দিন', 'category' => 'ক্লাস', 'labels' => ['নতুন', 'শিক্ষাবর্ষ']],
            ['title' => 'আমার সোনার বাংলা সাংস্কৃতিক অনুষ্ঠান', 'category' => 'সাংস্কৃতিক', 'labels' => ['সোনার', 'বাংলা']],
            ['title' => 'ক্যাম্পাসের সবুজ চত্বর', 'category' => 'ক্যাম্পাস', 'labels' => ['ক্যাম্পাস', 'পড়া']],
            ['title' => 'ডিজিটাল ক্লাসরুম কার্যক্রম', 'category' => 'ক্লাস', 'labels' => ['ডিজিটাল', 'ক্লাস']],
            ['title' => 'জাতীয় দিবস উদযাপন', 'category' => 'অনুষ্ঠান', 'labels' => ['জাতীয়', 'দিবস']],
            ['title' => 'বইমেলা ও লাইব্রেরি দিবস', 'category' => 'অনুষ্ঠান', 'labels' => ['বইমেলা', 'পড়া']],
        ];

        $gradients = [
            ['#f97316', '#ef4444'],
            ['#3b82f6', '#06b6d4'],
            ['#10b981', '#059669'],
            ['#8b5cf6', '#ec4899'],
            ['#f59e0b', '#f97316'],
            ['#06b6d4', '#3b82f6'],
            ['#ef4444', '#f97316'],
            ['#059669', '#10b981'],
        ];

        foreach ($items as $index => $item) {
            $filePath = 'gallery/ris-gallery-'.($index + 1).'.svg';

            if (! Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->put($filePath, $this->svgPlaceholder(
                    $item['labels'][0],
                    $item['labels'][1],
                    $gradients[$index][0],
                    $gradients[$index][1],
                ));
            }

            GalleryItem::create([
                'title' => $item['title'],
                'description' => $item['category'].'সম্পর্কিত স্মরণীয় মুহূর্ত।',
                'image' => $filePath,
                'category' => $item['category'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }

    protected function svgPlaceholder(string $lineOne, string $lineTwo, string $from, string $to): string
    {
        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="640" height="420" viewBox="0 0 640 420">
          <defs>
            <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="{$from}"/>
              <stop offset="100%" stop-color="{$to}"/>
            </linearGradient>
          </defs>
          <rect width="640" height="420" fill="url(#g)"/>
          <circle cx="560" cy="60" r="120" fill="#ffffff" opacity="0.12"/>
          <circle cx="60" cy="380" r="100" fill="#000000" opacity="0.10"/>
          <text x="320" y="200" font-family="Hind Siliguri, Arial, sans-serif" font-size="44" font-weight="700" fill="#ffffff" text-anchor="middle" opacity="0.95">{$lineOne}</text>
          <text x="320" y="258" font-family="Hind Siliguri, Arial, sans-serif" font-size="30" font-weight="600" fill="#ffffff" text-anchor="middle" opacity="0.85">{$lineTwo}</text>
        </svg>
        SVG;
    }
}
