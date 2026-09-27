<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClassRoom
 */
class ClassRoomResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'section' => $this->section,
            'sort_order' => $this->sort_order,
            'academic_year_id' => $this->academic_year_id,
            'class_teacher_id' => $this->class_teacher_id,
            'class_teacher' => $this->whenLoaded('classTeacher', fn () => $this->classTeacher ? [
                'id' => $this->classTeacher->id,
                'name' => $this->classTeacher->name,
            ] : null),
        ];
    }
}
