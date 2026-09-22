<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolStatistic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolStatisticController extends Controller
{
    public function index(): View
    {
        $statistic = SchoolStatistic::first();

        return view('admin.school-statistics.index', compact('statistic'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStats($request);

        try {
            if (SchoolStatistic::exists()) {
                return back()
                    ->with('error', 'পরিসংখ্যান আগে থেকেই যোগ করা আছে। সম্পাদনা করুন।');
            }

            SchoolStatistic::create($validated);

            return back()->with('success', 'পরিসংখ্যান সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'পরিসংখ্যান যোগ করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $statistic = SchoolStatistic::findOrFail($id);

        $validated = $this->validateStats($request);

        try {
            $statistic->update($validated);

            return back()->with('success', 'পরিসংখ্যান সফলভাবে আপডেট করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'পরিসংখ্যান আপডেট করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    /**
     * @return array{total_students: int, total_teachers: int, total_classes: int, total_staff: int, founding_year: int}
     */
    private function validateStats(Request $request): array
    {
        return $request->validate([
            'total_students' => 'required|integer|min:0|max:100000',
            'total_teachers' => 'required|integer|min:0|max:10000',
            'total_classes' => 'required|integer|min:0|max:100',
            'total_staff' => 'required|integer|min:0|max:10000',
            'founding_year' => 'required|integer|min:1900|max:'.((int) date('Y')),
        ], [
            'total_students.required' => 'মোট ছাত্র-ছাত্রী সংখ্যা আবশ্যক।',
            'total_students.integer' => 'মোট ছাত্র-ছাত্রী সংখ্যা সংখ্যা হতে হবে।',
            'total_students.min' => 'মোট ছাত্র-ছাত্রী সংখ্যা ০ বা তার বেশি হতে হবে।',
            'total_teachers.required' => 'শিক্ষক সংখ্যা আবশ্যক।',
            'total_teachers.integer' => 'শিক্ষক সংখ্যা সংখ্যা হতে হবে।',
            'total_teachers.min' => 'শিক্ষক সংখ্যা ০ বা তার বেশি হতে হবে।',
            'total_classes.required' => 'শ্রেণি সংখ্যা আবশ্যক।',
            'total_classes.integer' => 'শ্রেণি সংখ্যা সংখ্যা হতে হবে।',
            'total_classes.min' => 'শ্রেণি সংখ্যা ০ বা তার বেশি হতে হবে।',
            'total_staff.required' => 'কর্মচারী সংখ্যা আবশ্যক।',
            'total_staff.integer' => 'কর্মচারী সংখ্যা সংখ্যা হতে হবে।',
            'total_staff.min' => 'কর্মচারী সংখ্যা ০ বা তার বেশি হতে হবে।',
            'founding_year.required' => 'প্রতিষ্ঠাকাল আবশ্যক।',
            'founding_year.integer' => 'প্রতিষ্ঠাকাল সংখ্যা হতে হবে।',
            'founding_year.min' => 'প্রতিষ্ঠাকাল সঠিক নয়।',
            'founding_year.max' => 'প্রতিষ্ঠাকাল সঠিক নয়।',
        ]);
    }
}
