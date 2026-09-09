@extends('layouts.admin')

@section('title', 'নতুন মেধাবৃত্তি রেজিস্ট্রেশন')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.scholarship.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">নতুন মেধাবৃত্তি রেজিস্ট্রেশন</h1>
            <p class="text-sm text-gray-500 mt-1">শ্রেণি নির্বাচন করলে রেজিস্ট্রেশন নম্বর স্বয়ংক্রিয়ভাবে তৈরি হবে</p>
        </div>
    </div>

    <div class="max-w-3xl">
        @livewire('scholarship.registration-form', ['adminMode' => true])
    </div>

</div>
@endsection