<?php

/**
 * Models that can be restored / permanently deleted from the admin trash.
 * Keyed by a short URL-safe type key.
 */
return [
    'student' => [
        'model' => 'App\Models\Student',
        'label' => 'ছাত্র/ছাত্রী',
    ],
    'teacher' => [
        'model' => 'App\Models\User',
        'label' => 'শিক্ষক',
        'role' => 'teacher',
    ],
    'admission' => [
        'model' => 'App\Models\Admission',
        'label' => 'ভর্তি আবেদন',
    ],
    'scholarship' => [
        'model' => 'App\Models\ScholarshipRegistration',
        'label' => 'মেধাবৃত্তি রেজিস্ট্রেশন',
    ],
    'contact_message' => [
        'model' => 'App\Models\ContactMessage',
        'label' => 'কনটাক্ট মেসেজ',
    ],
    'notice' => [
        'model' => 'App\Models\Notice',
        'label' => 'নোটিশ',
    ],
    'class_room' => [
        'model' => 'App\Models\ClassRoom',
        'label' => 'শ্রেণি',
    ],
    'subject' => [
        'model' => 'App\Models\Subject',
        'label' => 'বিষয়',
    ],
    'staff' => [
        'model' => 'App\Models\Staff',
        'label' => 'কর্মচারী',
    ],
    'bus' => [
        'model' => 'App\Models\Bus',
        'label' => 'বাস',
    ],
    'book' => [
        'model' => 'App\Models\Book',
        'label' => 'বই',
    ],
    'book_borrowing' => [
        'model' => 'App\Models\BookBorrowing',
        'label' => 'বই ধার',
    ],
    'exam' => [
        'model' => 'App\Models\Exam',
        'label' => 'পরীক্ষা',
    ],
    'fee_structure' => [
        'model' => 'App\Models\FeeStructure',
        'label' => 'ফি স্ট্রাকচার',
    ],
    'fee_invoice' => [
        'model' => 'App\Models\FeeInvoice',
        'label' => 'ফি ইনভয়েস',
    ],
    'fee_payment' => [
        'model' => 'App\Models\FeePayment',
        'label' => 'ফি পেমেন্ট',
    ],
    'attendance' => [
        'model' => 'App\Models\Attendance',
        'label' => 'উপস্থিতি',
    ],
    'payroll' => [
        'model' => 'App\Models\Payroll',
        'label' => 'বেতন',
    ],
    'leave_request' => [
        'model' => 'App\Models\LeaveRequest',
        'label' => 'ছুটির আবেদন',
    ],
    'testimonial' => [
        'model' => 'App\Models\Testimonial',
        'label' => 'শুভকামনা ও মতামত',
    ],
    'gallery_item' => [
        'model' => 'App\Models\GalleryItem',
        'label' => 'গ্যালারি ছবি',
    ],
    'academic_calendar' => [
        'model' => 'App\Models\AcademicCalendar',
        'label' => 'একাডেমিক ক্যালেন্ডার',
    ],
    'message' => [
        'model' => 'App\Models\Message',
        'label' => 'বার্তা',
    ],
    'faq' => [
        'model' => 'App\Models\Faq',
        'label' => 'প্রশ্নোত্তর',
    ],
];
