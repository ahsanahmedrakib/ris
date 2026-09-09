<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Staff::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $staff = $query->latest()->paginate(15);

        return response()->json($staff);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|string|unique:staff,employee_id',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt(Str::random(10)),
                'role' => UserRole::Teacher->value,
                'is_active' => true,
            ]);

            $staff = Staff::create(array_merge($validated, ['user_id' => $user->id]));

            DB::commit();

            return response()->json([
                'message' => 'কর্মচারী সফলভাবে যোগ করা হয়েছে।',
                'staff' => $staff->load('user'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'কর্মচারী যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $staff = Staff::with(['user', 'payrolls'])->findOrFail($id);

        return response()->json($staff);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$staff->user_id}",
            'designation' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
        ]);

        try {
            if ($staff->user) {
                $staff->user->update(['name' => $validated['name'], 'email' => $validated['email']]);
            }
            $staff->update($validated);

            return response()->json([
                'message' => 'কর্মচারীর তথ্য সফলভাবে আপডেট হয়েছে।',
                'staff' => $staff->fresh()->load('user'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'কর্মচারী আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $staff = Staff::findOrFail($id);
            if ($staff->user) {
                $staff->user->update(['is_active' => false]);
            }
            $staff->delete();

            return response()->json(['message' => 'কর্মচারী সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'কর্মচারী মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
