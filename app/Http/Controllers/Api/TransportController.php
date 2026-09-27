<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TransportResource;
use App\Models\Bus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportController extends Controller
{
    public function index(): JsonResource
    {
        $buses = Bus::withCount(['busRoutes', 'studentTransports'])->latest()->get();

        return TransportResource::collection($buses);
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
                'bus' => new TransportResource($bus),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show(int $id): JsonResource
    {
        $bus = Bus::with(['busRoutes' => fn ($q) => $q->orderBy('stop_order'), 'studentTransports' => fn ($q) => $q->with('student.user')])->withCount(['busRoutes', 'studentTransports'])->findOrFail($id);

        return new TransportResource($bus);
    }

    public function update(Request $request, int $id): JsonResponse
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
                'bus' => new TransportResource($bus->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Bus::findOrFail($id)->delete();

            return response()->json(['message' => 'বাস সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বাস মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
