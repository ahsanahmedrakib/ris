<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Attendance
 */
class AttendanceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_id' => $this->class_id,
            'date' => $this->date?->toDateString(),
            'status' => $this->status,
            'marked_by' => $this->marked_by,
            'remarks' => $this->remarks,
        ];
    }
}
