<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payroll
 */
class PayrollResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'staff_id' => $this->staff_id,
            'month' => $this->month,
            'year' => $this->year,
            'basic_salary' => (float) $this->basic_salary,
            'allowances' => (float) $this->allowances,
            'deductions' => (float) $this->deductions,
            'net_salary' => (float) $this->net_salary,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'status' => $this->status,
        ];
    }
}
