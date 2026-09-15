<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'total_copies',
        'available_copies',
        'location',
        'description',
    ];

    public function bookBorrowings()
    {
        return $this->hasMany(BookBorrowing::class);
    }
}
