<?php

namespace App\Http\Controllers\Api;

use App\Enums\FeeStatus;
use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FeeInvoice::with(['student.user', 'feeStructure.classRoom']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(20);

        return response()->json($invoices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:fee_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_banking,cheque',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        try {
            $invoice = FeeInvoice::findOrFail($validated['invoice_id']);

            $newPaidAmount = $invoice->paid_amount + $validated['amount'];
            if ($newPaidAmount > $invoice->amount) {
                return response()->json(['message' => 'পরিশোধিত পরিমাণ বকেয়া পরিমাণের চেয়ে বেশি হতে পারবে না।'], 422);
            }

            $payment = FeePayment::create([
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

            return response()->json([
                'message' => 'পেমেন্ট সফলভাবে রেকর্ড করা হয়েছে।',
                'payment' => $payment->load(['student.user', 'invoice.feeStructure']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'পেমেন্ট রেকর্ড করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $invoice = FeeInvoice::with(['student.user', 'feeStructure', 'feePayments' => fn ($q) => $q->with('payer')])->findOrFail($id);

        return response()->json($invoice);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $invoice = FeeInvoice::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,partial,paid,overdue',
            'due_date' => 'nullable|date',
        ]);

        try {
            $invoice->update($validated);

            return response()->json([
                'message' => 'ফি চালান সফলভাবে আপডেট হয়েছে।',
                'invoice' => $invoice->fresh()->load(['student.user', 'feeStructure']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ফি চালান আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            FeeInvoice::findOrFail($id)->delete();

            return response()->json(['message' => 'ফি চালান সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ফি চালান মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
