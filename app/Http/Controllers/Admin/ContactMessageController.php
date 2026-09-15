<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $messages = $query->latest()->paginate($perPage)->withQueryString();

        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'breadcrumbs' => ['বার্তা' => null],
        ]);
    }

    public function show(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->update(['is_read' => true]);

        return response()->json([
            'id' => $contactMessage->id,
            'name' => $contactMessage->name,
            'email' => $contactMessage->email,
            'phone' => $contactMessage->phone,
            'subject' => $contactMessage->subject,
            'message' => $contactMessage->message,
            'is_read' => $contactMessage->is_read,
            'created_at' => $contactMessage->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['is_read' => ! $contactMessage->is_read]);

        return back()->with('success', $contactMessage->is_read ? 'পঠিত হিসাবে চিহ্নিত হয়েছে।' : 'অপঠিত হিসাবে চিহ্নিত হয়েছে।');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        try {
            $contactMessage->delete();

            return back()->with('success', 'বার্তা মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', 'বার্তা মুছে ফেলতে সমস্যা হয়েছে।');
        }
    }
}
