<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\StudentTransport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransportController extends Controller
{
    public function index(): View
    {
        $buses = Bus::withCount(['busRoutes', 'studentTransports'])->latest()->get();

        return view('admin.transport.index', compact('buses'));
    }

    public function create(): View
    {
        return view('admin.transport.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bus_no' => 'required|string|max:20|unique:buses,bus_no',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'route_name' => 'nullable|string|max:255',
        ], [
            'bus_no.required' => 'বাস নম্বর আবশ্যক।',
            'bus_no.unique' => 'এই বাস নম্বর ইতিমধ্যে বিদ্যমান।',
            'driver_name.required' => 'চালকের নাম আবশ্যক।',
            'driver_phone.required' => 'চালকের ফোন নম্বর আবশ্যক।',
            'capacity.required' => 'আসন সংখ্যা আবশ্যক।',
            'capacity.integer' => 'আসন সংখ্যা অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
            'capacity.min' => 'আসন সংখ্যা কমপক্ষে ১ হতে হবে।',
        ]);

        try {
            $validated['route_name'] = $validated['route_name'] ?? '';

            Bus::create($validated);

            return redirect()->route('admin.transport.index')
                ->with('success', 'বাস সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বাস যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show($id): View
    {
        $bus = Bus::with([
            'busRoutes' => fn ($q) => $q->orderBy('stop_order'),
            'studentTransports' => fn ($q) => $q->with('student.user'),
        ])->withCount(['busRoutes', 'studentTransports'])->findOrFail($id);

        return view('admin.transport.show', compact('bus'));
    }

    public function routes($busId): View
    {
        $bus = Bus::findOrFail($busId);
        $routes = BusRoute::where('bus_id', $busId)->orderBy('stop_order')->get();

        return view('admin.transport.routes', compact('bus', 'routes'));
    }

    public function storeRoute(Request $request, $busId): RedirectResponse
    {
        $validated = $request->validate([
            'stop_name' => 'required|string|max:255',
            'stop_time' => 'required|date_format:H:i',
            'stop_order' => 'required|integer|min:1',
        ], [
            'stop_name.required' => 'স্টপের নাম আবশ্যক।',
            'stop_time.required' => 'স্টপের সময় আবশ্যক।',
            'stop_time.date_format' => 'সঠিক সময় ফরম্যাট দিন (HH:MM)।',
            'stop_order.required' => 'স্টপের ক্রম আবশ্যক।',
            'stop_order.integer' => 'স্টপের ক্রম অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
        ]);

        try {
            BusRoute::create(array_merge($validated, [
                'bus_id' => $busId,
            ]));

            return redirect()->route('admin.transport.routes', $busId)
                ->with('success', 'বাস স্টপ সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বাস স্টপ যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function edit($id): View
    {
        $bus = Bus::findOrFail($id);

        return view('admin.transport.edit', compact('bus'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $bus = Bus::findOrFail($id);

        $validated = $request->validate([
            'bus_no' => "required|string|max:20|unique:buses,bus_no,{$bus->id}",
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'route_name' => 'nullable|string|max:255',
        ], [
            'bus_no.required' => 'বাস নম্বর আবশ্যক।',
            'bus_no.unique' => 'এই বাস নম্বর ইতিমধ্যে বিদ্যমান।',
            'driver_name.required' => 'চালকের নাম আবশ্যক।',
            'driver_phone.required' => 'চালকের ফোন নম্বর আবশ্যক।',
            'capacity.required' => 'আসন সংখ্যা আবশ্যক।',
        ]);

        try {
            $validated['route_name'] = $validated['route_name'] ?? '';

            $bus->update($validated);

            return redirect()->route('admin.transport.index')
                ->with('success', 'বাসের তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বাস আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $bus = Bus::findOrFail($id);

            if ($bus->studentTransports()->count() > 0) {
                return back()
                    ->with('error', 'এই বাসে ছাত্র/ছাত্রী নির্ধারিত আছে, তাই এটি মুছে ফেলা যাচ্ছে না।');
            }

            $bus->delete();

            return redirect()->route('admin.transport.index')
                ->with('success', 'বাস সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বাস মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function assignStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'nullable|exists:bus_routes,id',
            'pickup_stop' => 'nullable|string|max:255',
            'dropoff_stop' => 'nullable|string|max:255',
        ], [
            'student_id.required' => 'ছাত্র/ছাত্রী নির্বাচন আবশ্যক।',
            'student_id.exists' => 'নির্বাচিত ছাত্র/ছাত্রী বিদ্যমান নেই।',
            'bus_id.required' => 'বাস নির্বাচন আবশ্যক।',
            'bus_id.exists' => 'নির্বাচিত বাস বিদ্যমান নেই।',
            'route_id.exists' => 'নির্বাচিত রুট বিদ্যমান নেই।',
        ]);

        try {
            $bus = Bus::findOrFail($validated['bus_id']);
            $currentCount = $bus->studentTransports()->count();

            if ($currentCount >= $bus->capacity) {
                return back()->withInput()
                    ->with('error', 'এই বাসে আর কোনো আসন ফাঁকা নেই।');
            }

            StudentTransport::updateOrCreate(
                ['student_id' => $validated['student_id']],
                [
                    'bus_id' => $validated['bus_id'],
                    'route_id' => $validated['route_id'] ?? null,
                    'pickup_stop' => $validated['pickup_stop'] ?? null,
                    'dropoff_stop' => $validated['dropoff_stop'] ?? null,
                ]
            );

            return back()
                ->with('success', 'ছাত্র/ছাত্রী সফলভাবে বাসে নির্ধারিত হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ছাত্র/ছাত্রী নির্ধারণ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
