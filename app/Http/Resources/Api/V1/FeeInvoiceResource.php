<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FeeInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FeeInvoice
 */
class FeeInvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'admission_no' => $this->student->admission_no,
                'name' => $this->student->user?->name,
            ]),
            'fee_structure_id' => $this->fee_structure_id,
            'amount' => (float) $this->amount,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => round((float) $this->amount - (float) $this->paid_amount, 2),
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
        ];
    }
}
