<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $query = Staff::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $staff = $query->latest()->paginate(15)->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'required|string|unique:staff,employee_id',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:255',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'name.string' => 'নাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'employee_id.required' => 'কর্মচারী আইডি আবশ্যক।',
            'employee_id.unique' => 'এই কর্মচারী আইডি ইতিমধ্যে বিদ্যমান।',
            'designation.required' => 'পদবি আবশ্যক।',
            'joining_date.required' => 'যোগদানের তারিখ আবশ্যক।',
            'salary.required' => 'বেতন আবশ্যক।',
            'salary.numeric' => 'বেতন অবশ্যই একটি সংখ্যা হতে হবে।',
            'salary.min' => 'বেতন ০ এর বেশি হতে হবে।',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => bcrypt(Str::random(10)),
                'role' => UserRole::Teacher->value,
                'is_active' => true,
            ]);

            Staff::create(array_merge($validated, [
                'user_id' => $user->id,
            ]));

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'কর্মচারী সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'কর্মচারী যোগ করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $staff = Staff::with([
            'user',
            'payrolls' => fn ($q) => $q->latest('year')->latest('month'),
            'leaveRequests' => fn ($q) => $q->latest('start_date'),
        ])->findOrFail($id);

        return view('admin.staff.show', compact('staff'));
    }

    public function edit($id): View
    {
        $staff = Staff::with('user')->findOrFail($id);

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$staff->user_id}",
            'phone' => 'nullable|string|max:20',
            'employee_id' => "required|string|unique:staff,employee_id,{$staff->id}",
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:255',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'employee_id.required' => 'কর্মচারী আইডি আবশ্যক।',
            'employee_id.unique' => 'এই কর্মচারী আইডি ইতিমধ্যে বিদ্যমান।',
            'designation.required' => 'পদবি আবশ্যক।',
            'joining_date.required' => 'যোগদানের তারিখ আবশ্যক।',
            'salary.required' => 'বেতন আবশ্যক।',
        ]);

        try {
            DB::beginTransaction();

            $staff->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            $staff->update($validated);

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'কর্মচারীর তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'কর্মচারী আপডেট করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $staff = Staff::findOrFail($id);
            $staff->user->update(['is_active' => false]);
            $staff->delete();

            return redirect()->route('admin.staff.index')
                ->with('success', 'কর্মচারী সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'কর্মচারী মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
