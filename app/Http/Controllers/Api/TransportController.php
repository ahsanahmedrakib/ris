<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index(): JsonResponse
    {
        $buses = Bus::withCount(['busRoutes', 'studentTransports'])->latest()->get();

        return response()->json($buses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bus_no' => 'required|string|max:20|unique:buses,bus_no',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'route_name' => 'nullable|string|max:255',
        ]);

        try {
            $validated['route_name'] = $validated['route_name'] ?? '';

            $bus = Bus::create($validated);

            return response()->json([
                'message' => 'বাস সফলভাবে যোগ করা হয়েছে।',
                'bus' => $bus,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $bus = Bus::with(['busRoutes' => fn ($q) => $q->orderBy('stop_order'), 'studentTransports' => fn ($q) => $q->with('student.user')])->withCount(['busRoutes', 'studentTransports'])->findOrFail($id);

        return response()->json($bus);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $bus = Bus::findOrFail($id);

        $validated = $request->validate([
            'bus_no' => "required|string|max:20|unique:buses,bus_no,{$bus->id}",
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'route_name' => 'nullable|string|max:255',
        ]);

        try {
            $validated['route_name'] = $validated['route_name'] ?? '';

            $bus->update($validated);

            return response()->json([
                'message' => 'বাসের তথ্য সফলভাবে আপডেট হয়েছে।',
                'bus' => $bus->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Bus::findOrFail($id)->delete();

            return response()->json(['message' => 'বাস সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
