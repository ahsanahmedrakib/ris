<?php

/*
| Everything a search engine, a link preview or a knowledge panel needs to know
| about the school, kept in one place so a correction (a new phone number, a
| renamed Facebook page) is a one line change rather than a hunt through every
| blade template.
|
| The pages themselves are written in Bengali, because the readers are. The
| metadata is English, because the searches mostly are: someone types "resma
| international school admission" into a phone in English and expects a result.
| Google matches a page on its metadata as well as its body text, so an English
| description on a Bengali page is what makes those searches land.
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Identity
    |--------------------------------------------------------------------------
    |
    | The name as it should appear in search results, and the name most people
    | actually type. "Resma International School" is the Latin spelling on the
    | logo; the Bengali name is what visitors read once they arrive.
    |
    */

    'name' => env('SEO_SITE_NAME', 'Resma International School'),

    'name_bn' => 'রেশমা ইন্টারন্যাশনাল স্কুল',

    /*
    | Keyword variants of the same school. These are not stuffed into the page;
    | they exist so a search for a misspelling, an old spelling or a
    | transliteration still finds the school.
    |
    */

    'aliases' => [
        'Resma International School',
        'Resma International School Gopalganj',
        'Resma International School Bangladesh',
        'RIS Gopalganj',
        'Reśmā International School',
    ],

    /*
    |--------------------------------------------------------------------------
    | Name, Address, Phone
    |--------------------------------------------------------------------------
    |
    | Local results and knowledge panels are built from these three agreeing with
    | each other. They must match the footer and the contact page exactly, or
    | Google treats them as two different places and ranks neither.
    |
    | The phone is in E.164 so it can be linked with tel: and handed to a
    | dialler without a visitor retyping it.
    |
    */

    'address' => [
        'street' => '৪৩৯, ঘুল্লিবাড়ি মোড়',
        'address_locality' => 'গোপালগঞ্জ',
        'address_region' => 'গোপালগঞ্জ জেলা',
        'postal_code' => '8100',
        'address_country' => 'BD',
    ],

    'phone' => '+8801619007006',

    'phone_display' => '+৮৮০-১৬১৯ ০০৭ ০০৬',

    'email' => 'resmaintlschool@gmail.com',

    /*
    |--------------------------------------------------------------------------
    | Social profiles
    |--------------------------------------------------------------------------
    |
    | SameProfile links tell a search engine these accounts are the same entity.
    | Only list a profile that actually exists.
    |
    */

    'social' => [
        'facebook' => 'https://www.facebook.com/Resma.International.School',
        'youtube' => null,
        'linkedin' => null,
        'instagram' => null,
        'twitter' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ownership signals
    |--------------------------------------------------------------------------
    |
    | Google uses the language a site is written in to decide which country and
    | language its results belong to. Both are stated here because the site
    | publishes in Bengali but is searched for in English.
    |
    */

    'locale' => 'bn_BD',

    'locale_alternate' => 'en_US',

    'country' => 'BD',

    'geo' => [
        'latitude' => 23.8555,
        'longitude' => 90.4125,
    ],

    /*
    |--------------------------------------------------------------------------
    | Type of organisation
    |--------------------------------------------------------------------------
    |
    | Drives which schema.org type is published as the organisation. A private
    | school is EducationalOrganization, which Google treats as a school; "School"
    | is the narrower subtype it prefers for the knowledge panel.
    |
    */

    'type' => 'School',

    'price_range' => '৳৳',

    /*
    |--------------------------------------------------------------------------
    | Search Console and analytics
    |--------------------------------------------------------------------------
    |
    | Both are opt-in and empty by default. The verification token goes in the
    | head as a meta tag, which is the least invasive of the methods Google
    | offers: nothing on the server has to change and the tag can be removed
    | without affecting the property.
    |
    */

    'google_site_verification' => env('SEO_GOOGLE_SITE_VERIFICATION'),

    'google_analytics_id' => env('SEO_GOOGLE_ANALYTICS_ID'),

    'bing_site_verification' => env('SEO_BING_SITE_VERIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Preview image
    |--------------------------------------------------------------------------
    |
    | The image used when the page is shared on Facebook, WhatsApp or iMessage.
    | A link with no image shares as a bare grey box, which suppresses clicks.
    |
    */

    'og_image' => 'assets/og-image.jpg',

    'og_image_alt' => 'Resma International School, Gopalganj',

    'twitter_site' => null,

    /*
    |--------------------------------------------------------------------------
    | Sitemap
    |--------------------------------------------------------------------------
    */

    'sitemap_path' => 'sitemap.xml',

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | One entry per public URL, keyed by route name. Each supplies the English
    | title and description, and the breadcrumb trail above the page heading.
    |
    | The descriptions are the whole point. They are the text Google shows under
    | the link and the only English on most of these pages, so they are written
    | to be read on a phone: what the page offers, and the words somebody types
    | to find it. They are not a keyword list, because Google has not read the
    | keywords meta tag since 2009 and stuffing a description reads as spam to a
    | human who sees it in the results.
    |
    | A route named here with no view changes needed still gets a canonical URL,
    | an Open Graph card and a breadcrumb, which is why this map exists rather
    | than per-page blade edits alone.
    |
    */

    'pages' => [

        'home' => [
            'title' => 'রেশমা ইন্টারন্যাশনাল স্কুল - Resma International School',
            'description' => 'Resma International School is a school in Gopalganj offering admission from nursery to secondary level, merit scholarships, a digital classroom and a science laboratory.',
            'breadcrumbs' => [],
        ],

        'about' => [
            'title' => 'আমাদের সম্পর্কে । About Us | Resma International School',
            'description' => 'Our mission, vision and core values: how Resma International School in Gopalganj builds confident, curious students through quality education.',
            'breadcrumbs' => ['About Us'],
        ],

        'admission' => [
            'title' => 'ভর্তি । Admission | Resma International School',
            'description' => 'Apply for admission to Resma International School, Gopalganj. Fill the online admission form, see the required documents and class-wise fees for the 2026 academic year.',
            'breadcrumbs' => ['Admission'],
        ],

        'scholarship' => [
            'title' => 'মেধাবৃত্তি । Merit Scholarship | Resma International School',
            'description' => 'Apply for the merit scholarship at Resma International School, Gopalganj. Open to meritorious and financially disadvantaged students. Online registration, no cost.',
            'breadcrumbs' => ['Scholarship'],
        ],

        'contact' => [
            'title' => 'যোগাযোগ | Contact Us | Resma International School',
            'description' => 'Address, phone number and email of Resma International School, 439 Ghullibari Mor, Gopalganj 8100, Bangladesh. Send us a message or call to arrange a visit.',
            'breadcrumbs' => ['Contact'],
        ],

        'notices' => [
            'title' => 'নোটিশ | Notice Board | Resma International School',
            'description' => 'Exam schedules, holidays, admission dates and school notices from the administration of Resma International School, Gopalganj.',
            'breadcrumbs' => ['Notices'],
        ],

        'campus-events' => [
            'title' => 'ক্যাম্পাস ইভেন্ট | Campus Events | Resma International School',
            'description' => 'Events, activities and memorable moments from Resma International School, Gopalganj: science fairs, cultural programmes, sports days and excursions.',
            'breadcrumbs' => ['Campus Events'],
        ],

        /*
        | A notice, a campus event item and a teacher are described by their own
        | content, which the controller hands over as an override. Without one
        | these fall back to the section label rather than to nothing, because an
        | empty description is what these pages had before.
        */
        'notices.single' => [
            'title' => 'Notice',
            'description' => 'Official notice from Resma International School, Gopalganj.',
            'breadcrumbs' => ['Notices'],
            'dynamic' => true,
        ],

        'campus-events.single' => [
            'title' => 'Campus Events',
            'description' => 'Events and activities at Resma International School, Gopalganj.',
            'breadcrumbs' => ['Campus Events'],
            'dynamic' => true,
        ],

        'teachers' => [
            'title' => 'আমাদের শিক্ষক-শিক্ষিকাবৃন্দ | Our Teachers | Resma International School',
            'description' => 'Meet the teachers of Resma International School, Gopalganj: their qualifications, subjects taught and years of classroom experience.',
            'breadcrumbs' => ['Teachers'],
        ],

        'teacher.single' => [
            'title' => 'Our Teacher',
            'description' => 'Teacher profile at Resma International School, Gopalganj: qualification, subject taught and classroom experience.',
            'breadcrumbs' => ['Teachers'],
            'dynamic' => true,
        ],

        'testimonials' => [
            'title' => 'শুভকামনা | Testimonials | Resma International School',
            'description' => 'What parents and students say about Resma International School, Gopalganj. Leave a review about the school, the teachers or admission.',
            'breadcrumbs' => ['Testimonials'],
        ],

        'gallery' => [
            'title' => 'গ্যালারি | Photo Gallery | Resma International School',
            'description' => 'Photographs of Resma International School, Gopalganj: the campus, annual events, sports day, science fair and academic activities.',
            'breadcrumbs' => ['Gallery'],
        ],

        'class-routine' => [
            'title' => 'ক্লাশ রুটিন | Class Routine | Resma International School',
            'description' => 'Weekly class routine for every class at Resma International School, Gopalganj, with subject timings and teacher names.',
            'breadcrumbs' => ['Class Routine'],
        ],

        'class-routine.grid' => [
            'title' => 'ক্লাশ রুটিন | Class Routine | Resma International School',
            'description' => 'Weekly class routine with subject timings and teacher names for every class at Resma International School, Gopalganj.',
            'breadcrumbs' => ['Class Routine'],
        ],

        'academic.calendar' => [
            'title' => 'একাডেমিক ক্যালেন্ডার | Academic Calendar | Resma International School',
            'description' => 'The 2026 academic calendar for Resma International School, Gopalganj: term dates, exam schedules, holidays and result publication days.',
            'breadcrumbs' => ['Academic Calendar'],
        ],

        'academic.fees' => [
            'title' => 'টিউশন ফি | Tuition Fees | Resma International School',
            'description' => 'Class-wise tuition fees and session charges for Resma International School, Gopalganj for the current academic year.',
            'breadcrumbs' => ['Tuition Fees'],
        ],

        'academic.facilities' => [
            'title' => 'স্কুলের সুবিধা | School Facilities | Resma International School',
            'description' => 'Facilities at Resma International School, Gopalganj: digital classroom, science laboratory, library, computer lab, playground and transport.',
            'breadcrumbs' => ['Facilities'],
        ],

        /*
        | Results are behind authentication and belong to individual children.
        | The noindex is what actually keeps a child's marks out of an index;
        | the Disallow in robots.txt alone only asks the crawler not to look.
        */
        'academic.results' => [
            'title' => 'পরীক্ষার ফলাফল | Examination Results | Resma International School',
            'description' => '',
            'breadcrumbs' => ['Results'],
            'noindex' => true,
        ],
    ],
];
