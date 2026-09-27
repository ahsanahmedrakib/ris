<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(15);

        return BookResource::collection($books);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'category' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            $validated['category'] = $validated['category'] ?? '';

            $book = Book::create($validated);

            return response()->json([
                'message' => 'বই সফলভাবে যোগ করা হয়েছে।',
                'book' => new BookResource($book),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বই যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show(int $id): JsonResource
    {
        $book = Book::with(['bookBorrowings' => fn ($q) => $q->with('student.user')])->findOrFail($id);

        return new BookResource($book);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => "required|string|unique:books,isbn,{$book->id}",
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
        ]);

        try {
            $book->update($validated);

            return response()->json([
                'message' => 'বইয়ের তথ্য সফলভাবে আপডেট হয়েছে।',
                'book' => new BookResource($book->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বই আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Book::findOrFail($id)->delete();

            return response()->json(['message' => 'বই সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বই মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
