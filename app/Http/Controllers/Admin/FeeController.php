<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FeeStatus;
use App\Enums\FeeType;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeeController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_collected' => FeePayment::sum('amount'),
            'total_pending' => FeeInvoice::whereIn('status', [FeeStatus::Pending, FeeStatus::Partial])->sum('amount'),
            'total_overdue' => FeeInvoice::where('status', FeeStatus::Overdue)->sum('amount'),
            'total_invoices' => FeeInvoice::count(),
        ];

        $recentPayments = FeePayment::with(['student.user', 'invoice.feeStructure'])
            ->latest('paid_at')
            ->take(10)
            ->get();

        return view('admin.fees.index', compact('stats', 'recentPayments'));
    }

    public function structures(): View
    {
        $structures = FeeStructure::with(['classRoom', 'academicYear'])->latest()->get();

        return view('admin.fees.structures', compact('structures'));
    }

    public function createStructure(): View
    {
        $classes = ClassRoom::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_current')->orderByDesc('name')->get();
        $feeTypes = FeeType::cases();

        return view('admin.fees.create-structure', compact('classes', 'academicYears', 'feeTypes'));
    }

    public function storeStructure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_type' => 'required|string|in:tuition,transport,library,exam,others',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date',
        ], [
            'class_id.required' => 'শ্রেণী নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণী বিদ্যমান নেই।',
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
            'academic_year_id.exists' => 'নির্বাচিত শিক্ষাবর্ষ বিদ্যমান নেই।',
            'fee_type.required' => 'ফি-এর ধরন আবশ্যক।',
            'fee_type.in' => 'সঠিক ফি-এর ধরন নির্বাচন করুন।',
            'amount.required' => 'পরিমাণ আবশ্যক।',
            'amount.numeric' => 'পরিমাণ অবশ্যই একটি সংখ্যা হতে হবে।',
            'amount.min' => 'পরিমাণ ০ এর বেশি হতে হবে।',
            'due_date.required' => 'শেষ তারিখ আবশ্যক।',
        ]);

        try {
            FeeStructure::create($validated);

            return redirect()->route('admin.fees.structures')
                ->with('success', 'ফি কাঠামো সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ফি কাঠামো তৈরি করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function invoices(Request $request): View
    {
        $query = FeeInvoice::with(['student.user', 'feeStructure.classRoom']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $request->class_id));
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();
        $statuses = FeeStatus::cases();

        return view('admin.fees.invoices', compact('invoices', 'classes', 'statuses'));
    }

    public function generateInvoices(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fee_structure_id' => 'required|exists:fee_structures,id',
        ], [
            'fee_structure_id.required' => 'ফি কাঠামো নির্বাচন আবশ্যক।',
            'fee_structure_id.exists' => 'নির্বাচিত ফি কাঠামো বিদ্যমান নেই।',
        ]);

        try {
            $feeStructure = FeeStructure::findOrFail($validated['fee_structure_id']);
            $students = Student::where('class_id', $feeStructure->class_id)
                ->where('is_active', true)
                ->get();

            $count = 0;
            foreach ($students as $student) {
                $exists = FeeInvoice::where('student_id', $student->id)
                    ->where('fee_structure_id', $feeStructure->id)
                    ->exists();

                if (! $exists) {
                    FeeInvoice::create([
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructure->id,
                        'amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'due_date' => $feeStructure->due_date,
                        'status' => FeeStatus::Pending->value,
                    ]);
                    $count++;
                }
            }

            return redirect()->route('admin.fees.invoices')
                ->with('success', "{$count}টি ফি চালান সফলভাবে তৈরি হয়েছে।");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ফি চালান তৈরি করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function payments(): View
    {
        $payments = FeePayment::with(['student.user', 'invoice.feeStructure', 'payer'])
            ->latest('paid_at')
            ->paginate(20);

        return view('admin.fees.payments', compact('payments'));
    }

    public function recordPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:fee_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,mobile_banking,cheque',
            'transaction_id' => 'nullable|string|max:100',
        ], [
            'invoice_id.required' => 'চালান নির্বাচন আবশ্যক।',
            'invoice_id.exists' => 'নির্বাচিত চালান বিদ্যমান নেই।',
            'amount.required' => 'পরিমাণ আবশ্যক।',
            'amount.numeric' => 'পরিমাণ অবশ্যই একটি সংখ্যা হতে হবে।',
            'amount.min' => 'পরিমাণ ০.০১ এর বেশি হতে হবে।',
            'payment_method.required' => 'পেমেন্ট পদ্ধতি আবশ্যক।',
            'payment_method.in' => 'সঠিক পেমেন্ট পদ্ধতি নির্বাচন করুন।',
        ]);

        try {
            $invoice = FeeInvoice::findOrFail($validated['invoice_id']);

            $newPaidAmount = $invoice->paid_amount + $validated['amount'];
            if ($newPaidAmount > $invoice->amount) {
                return back()->withInput()
                    ->with('error', 'পরিশোধিত পরিমাণ বকেয়া পরিমাণের চেয়ে বেশি হতে পারবে না।');
            }

            FeePayment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            $invoice->paid_amount = $newPaidAmount;
            $invoice->status = $newPaidAmount >= $invoice->amount
                ? FeeStatus::Paid->value
                : FeeStatus::Partial->value;
            $invoice->save();

            return redirect()->route('admin.fees.payments')
                ->with('success', 'পেমেন্ট সফলভাবে রেকর্ড করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'পেমেন্ট রেকর্ড করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $invoice = FeeInvoice::with(['student.user', 'feeStructure'])->findOrFail($id);

        return view('admin.fees.edit', compact('invoice'));
    }

    public function destroy($id): RedirectResponse
    {
        try {
            FeeInvoice::findOrFail($id)->delete();

            return redirect()->route('admin.fees.invoices')
                ->with('success', 'ফি চালান সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ফি চালান মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
