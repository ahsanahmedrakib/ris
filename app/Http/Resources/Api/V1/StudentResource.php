<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Student records carry a minor's personal data, so the field list is explicit.
 * Adding a column to the model must never widen what the API hands out.
 *
 * @mixin Student
 */
class StudentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_no' => $this->admission_no,
            'name' => $this->user?->name,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'avatar' => $this->user?->avatar_url,
            'class_id' => $this->class_id,
            'class' => $this->whenLoaded('classRoom', fn () => [
                'id' => $this->classRoom->id,
                'name' => $this->classRoom->name,
                'section' => $this->classRoom->section,
            ]),
            'section' => $this->section,
            'roll_no' => $this->roll_no,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'address' => $this->address,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'guardian_email' => $this->guardian_email,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
