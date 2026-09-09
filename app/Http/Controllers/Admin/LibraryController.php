<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(15)->withQueryString();

        return view('admin.library.index', compact('books'));
    }

    public function create(): View
    {
        return view('admin.library.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'category' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'বইয়ের শিরোনাম আবশ্যক।',
            'title.string' => 'শিরোনাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'author.required' => 'লেখকের নাম আবশ্যক।',
            'isbn.required' => 'ISBN আবশ্যক।',
            'isbn.unique' => 'এই ISBN ইতিমধ্যে বিদ্যমান।',
            'total_copies.required' => 'মোট কপি সংখ্যা আবশ্যক।',
            'total_copies.integer' => 'মোট কপি সংখ্যা অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
            'total_copies.min' => 'মোট কপি সংখ্যা কমপক্ষে ১ হতে হবে।',
            'available_copies.required' => 'উপলব্ধ কপি সংখ্যা আবশ্যক।',
            'available_copies.lte' => 'উপলব্ধ কপি মোট কপির সমান বা কম হতে হবে।',
        ]);

        try {
            Book::create($validated);

            return redirect()->route('admin.library.index')
                ->with('success', 'বই সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বই যোগ করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $book = Book::with([
            'bookBorrowings' => fn ($q) => $q->with('student.user')->latest('borrowed_at'),
        ])->withCount('bookBorrowings')->findOrFail($id);

        return view('admin.library.show', compact('book'));
    }

    public function edit($id): View
    {
        $book = Book::findOrFail($id);

        return view('admin.library.edit', compact('book'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => "required|string|unique:books,isbn,{$book->id}",
            'category' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'বইয়ের শিরোনাম আবশ্যক।',
            'author.required' => 'লেখকের নাম আবশ্যক।',
            'isbn.required' => 'ISBN আবশ্যক।',
            'isbn.unique' => 'এই ISBN ইতিমধ্যে বিদ্যমান।',
            'total_copies.required' => 'মোট কপি সংখ্যা আবশ্যক।',
            'available_copies.required' => 'উপলব্ধ কপি সংখ্যা আবশ্যক।',
            'available_copies.lte' => 'উপলব্ধ কপি মোট কপির সমান বা কম হতে হবে।',
        ]);

        try {
            $book->update($validated);

            return redirect()->route('admin.library.index')
                ->with('success', 'বইয়ের তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বই আপডেট করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $book = Book::findOrFail($id);

            $activeBorrowings = BookBorrowing::where('book_id', $id)
                ->whereNull('returned_at')
                ->count();

            if ($activeBorrowings > 0) {
                return back()
                    ->with('error', 'এই বইটি এখনও ধার নেওয়া হয়েছে, তাই এটি মুছে ফেলা যাচ্ছে না।');
            }

            $book->delete();

            return redirect()->route('admin.library.index')
                ->with('success', 'বই সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বই মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function borrowings(): View
    {
        $borrowings = BookBorrowing::with(['book', 'student.user'])
            ->latest('borrowed_at')
            ->paginate(20);

        return view('admin.library.borrowings', compact('borrowings'));
    }

    public function borrow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'due_at' => 'required|date|after:today',
        ], [
            'book_id.required' => 'বই নির্বাচন আবশ্যক।',
            'book_id.exists' => 'নির্বাচিত বই বিদ্যমান নেই।',
            'student_id.required' => 'ছাত্র/ছাত্রী নির্বাচন আবশ্যক।',
            'student_id.exists' => 'নির্বাচিত ছাত্র/ছাত্রী বিদ্যমান নেই।',
            'due_at.required' => 'ফেরার তারিখ আবশ্যক।',
            'due_at.after' => 'ফেরার তারিখ আজকের পরে হতে হবে।',
        ]);

        try {
            $book = Book::findOrFail($validated['book_id']);

            if ($book->available_copies <= 0) {
                return back()->withInput()
                    ->with('error', 'এই বইটির কোনো কপি উপলব্ধ নেই।');
            }

            BookBorrowing::create([
                'book_id' => $validated['book_id'],
                'student_id' => $validated['student_id'],
                'borrowed_at' => now(),
                'due_at' => $validated['due_at'],
                'status' => 'borrowed',
            ]);

            $book->decrement('available_copies');

            return redirect()->route('admin.library.borrowings')
                ->with('success', 'বই সফলভাবে ধার দেওয়া হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বই ধার দিতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function returnBook($id): RedirectResponse
    {
        try {
            $borrowing = BookBorrowing::with('book')->findOrFail($id);

            if ($borrowing->returned_at) {
                return back()
                    ->with('error', 'এই বইটি ইতিমধ্যে ফেরত দেওয়া হয়েছে।');
            }

            $borrowing->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $borrowing->book->increment('available_copies');

            return redirect()->route('admin.library.borrowings')
                ->with('success', 'বই সফলভাবে ফেরত নেওয়া হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বই ফেরত নিতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
