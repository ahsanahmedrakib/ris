<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Staff
 */
class StaffResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'name' => $this->user?->name,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'designation' => $this->designation,
            'department' => $this->department,
            'joining_date' => $this->joining_date?->toDateString(),
            'qualification' => $this->qualification,
        ];
    }
}
