<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

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
