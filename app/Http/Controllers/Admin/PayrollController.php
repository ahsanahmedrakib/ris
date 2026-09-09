<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payroll::with('staff.user');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $payrolls = $query->latest('year')->latest('month')->paginate(20)->withQueryString();

        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);

        return view('admin.payroll.index', compact('payrolls', 'years'));
    }

    public function create(): View
    {
        $staff = Staff::with('user')->get();
        $currentMonth = date('m');
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);

        return view('admin.payroll.create', compact('staff', 'currentMonth', 'currentYear', 'years'));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->processPayroll($request);
    }

    public function process(Request $request): RedirectResponse
    {
        return $this->processPayroll($request);
    }

    private function processPayroll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staff,id',
            'allowances' => 'nullable|array',
            'allowances.*' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*' => 'nullable|numeric|min:0',
        ], [
            'month.required' => 'মাস নির্বাচন আবশ্যক।',
            'month.integer' => 'মাস অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
            'month.min' => 'মাস ১ এর কম হতে পারবে না।',
            'month.max' => 'মাস ১২ এর বেশি হতে পারবে না।',
            'year.required' => 'বছর নির্বাচন আবশ্যক।',
            'year.integer' => 'বছর অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
        ]);

        try {
            $staffToProcess = ! empty($validated['staff_ids'])
                ? Staff::whereIn('id', $validated['staff_ids'])->get()
                : Staff::all();

            $count = 0;
            foreach ($staffToProcess as $index => $staffMember) {
                $exists = Payroll::where('staff_id', $staffMember->id)
                    ->where('month', $validated['month'])
                    ->where('year', $validated['year'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $allowance = $validated['allowances'][$index] ?? 0;
                $deduction = $validated['deductions'][$index] ?? 0;
                $netSalary = $staffMember->salary + $allowance - $deduction;

                Payroll::create([
                    'staff_id' => $staffMember->id,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'basic_salary' => $staffMember->salary,
                    'allowances' => $allowance,
                    'deductions' => $deduction,
                    'net_salary' => $netSalary,
                    'status' => 'pending',
                ]);
                $count++;
            }

            return redirect()->route('admin.payroll.index')
                ->with('success', "{$count}টি বেতন স্লিপ সফলভাবে তৈরি হয়েছে।");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বেতন প্রক্রিয়া করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $payroll = Payroll::with(['staff.user'])->findOrFail($id);

        return view('admin.payroll.show', compact('payroll'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,paid',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ], [
            'status.required' => 'অবস্থা নির্বাচন আবশ্যক।',
            'status.in' => 'সঠিক অবস্থা নির্বাচন করুন।',
        ]);

        try {
            $data = ['status' => $validated['status']];

            if ($validated['status'] === 'paid') {
                $data['paid_at'] = now();
            }

            if (isset($validated['allowances']) || isset($validated['deductions'])) {
                $allowance = $validated['allowances'] ?? $payroll->allowances;
                $deduction = $validated['deductions'] ?? $payroll->deductions;
                $data['allowances'] = $allowance;
                $data['deductions'] = $deduction;
                $data['net_salary'] = $payroll->basic_salary + $allowance - $deduction;
            }

            $payroll->update($data);

            $message = $validated['status'] === 'paid'
                ? 'বেতন সফলভাবে পরিশোধিত হিসাবে চিহ্নিত হয়েছে।'
                : 'বেতনের অবস্থা সফলভাবে আপডেট হয়েছে।';

            return redirect()->route('admin.payroll.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বেতন আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
