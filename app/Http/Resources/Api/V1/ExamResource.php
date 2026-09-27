<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Exam
 */
class ExamResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'class_id' => $this->class_id,
            'academic_year_id' => $this->academic_year_id,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'total_marks' => $this->total_marks,
            'passing_marks' => $this->passing_marks,
        ];
    }
}
