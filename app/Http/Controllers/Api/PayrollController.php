<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payroll::with('staff.user');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $payrolls = $query->latest('year')->latest('month')->paginate(20);

        return response()->json($payrolls);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        try {
            $staffMember = Staff::findOrFail($validated['staff_id']);

            $exists = Payroll::where('staff_id', $validated['staff_id'])
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'এই মাসের জন্য এই কর্মচারীর বেতন ইতিমধ্যে প্রক্রিয়াকৃত।'], 422);
            }

            $allowance = $validated['allowances'] ?? 0;
            $deduction = $validated['deductions'] ?? 0;
            $netSalary = $staffMember->salary + $allowance - $deduction;

            $payroll = Payroll::create([
                'staff_id' => $validated['staff_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'basic_salary' => $staffMember->salary,
                'allowances' => $allowance,
                'deductions' => $deduction,
                'net_salary' => $netSalary,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'বেতন স্লিপ সফলভাবে তৈরি হয়েছে।',
                'payroll' => $payroll->load('staff.user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বেতন প্রক্রিয়া করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $payroll = Payroll::with('staff.user')->findOrFail($id);

        return response()->json($payroll);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid',
        ]);

        try {
            $data = ['status' => $validated['status']];
            if ($validated['status'] === 'paid') {
                $data['paid_at'] = now();
            }

            $payroll->update($data);

            return response()->json([
                'message' => 'বেতন সফলভাবে আপডেট হয়েছে।',
                'payroll' => $payroll->fresh()->load('staff.user'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বেতন আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Payroll::findOrFail($id)->delete();

            return response()->json(['message' => 'বেতন স্লিপ সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বেতন স্লিপ মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
