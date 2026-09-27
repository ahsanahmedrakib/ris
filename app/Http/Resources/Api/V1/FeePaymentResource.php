<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FeePayment
 */
class FeePaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'student_id' => $this->student_id,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'paid_by' => $this->whenLoaded('payer', fn () => $this->payer?->only(['id', 'name'])),
            'paid_at' => $this->paid_at?->toIso8601String(),
        ];
    }
}
