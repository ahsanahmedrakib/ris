<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const CATEGORIES = [
        'admission' => 'ভর্তি',
        'exam' => 'পরীক্ষা',
        'holiday' => 'ছুটি',
        'general' => 'সাধারণ',
    ];

    protected $fillable = [
        'title',
        'content',
        'type',
        'category',
        'target_role',
        'published_by',
        'published_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
