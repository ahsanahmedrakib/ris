<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Table;

class ScholarshipController extends Controller
{
    public const STATUSES = [
        'pending' => 'পেন্ডিং',
        'approved' => 'অ্যাকসেপ্টেড',
        'rejected' => 'রিজেক্টেড',
    ];

    public function index(Request $request): View
    {
        $query = ScholarshipRegistration::with('creator');

        if ($request->filled('class_no')) {
            $query->where('class_no', $request->class_no);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 15;
        }

        $registrations = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.scholarship.index', [
            'registrations' => $registrations,
            'classes' => ScholarshipRegistration::CLASSES,
            'statuses' => self::STATUSES,
            'breadcrumbs' => ['মেধাবৃত্তি' => null],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'class_no' => 'required|in:1,2,3,4,5',
            'roll_no' => 'required|string|max:20',
            'mobile_no' => 'required|regex:/^01[0-9]{9}$/',
            'bkash_no' => 'nullable|regex:/^01[0-9]{9}$/',
            'payment_method' => 'required|in:bkash,cash',
            'status' => 'required|in:pending,approved,rejected',
        ], [
            'student_name.required' => 'শিক্ষার্থীর নাম আবশ্যক।',
            'father_name.required' => 'পিতার নাম আবশ্যক।',
            'mother_name.required' => 'মাতার নাম আবশ্যক।',
            'school_name.required' => 'স্কুলের নাম আবশ্যক।',
            'class_no.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'roll_no.required' => 'রোল নং আবশ্যক।',
            'mobile_no.required' => 'মোবাইল নম্বর আবশ্যক।',
            'mobile_no.regex' => 'সঠিক মোবাইল নম্বর দিন।',
            'bkash_no.regex' => 'সঠিক বিকাশ নম্বর দিন।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম নির্বাচন করুন।',
            'status.required' => 'স্ট্যাটাস নির্বাচন করুন।',
        ]);

        try {
            $classNo = (int) $validated['class_no'];
            $number = ScholarshipRegistration::nextRegistrationNo($classNo);

            ScholarshipRegistration::create([
                'registration_no' => $number,
                'serial_no' => (int) substr($number, -3),
                'student_name' => $validated['student_name'],
                'father_name' => $validated['father_name'],
                'mother_name' => $validated['mother_name'],
                'school_name' => $validated['school_name'],
                'class_no' => $classNo,
                'roll_no' => $validated['roll_no'] ?? null,
                'mobile_no' => $validated['mobile_no'],
                'bkash_no' => $validated['payment_method'] === 'cash' ? null : ($validated['bkash_no'] ?? null),
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.scholarship.index')
                ->with('success', 'মেধাবৃত্তি রেজিস্ট্রেশন ('.$number.') সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'রেজিস্ট্রেশন তৈরি করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(ScholarshipRegistration $scholarshipRegistration)
    {
        $scholarshipRegistration->load('creator');

        return response()->json([
            'id' => $scholarshipRegistration->id,
            'registration_no' => $scholarshipRegistration->registration_no,
            'student_name' => $scholarshipRegistration->student_name,
            'father_name' => $scholarshipRegistration->father_name,
            'mother_name' => $scholarshipRegistration->mother_name,
            'school_name' => $scholarshipRegistration->school_name,
            'class_no' => $scholarshipRegistration->class_no,
            'class_name' => ScholarshipRegistration::CLASSES[$scholarshipRegistration->class_no] ?? '-',
            'roll_no' => $scholarshipRegistration->roll_no,
            'mobile_no' => $scholarshipRegistration->mobile_no,
            'bkash_no' => $scholarshipRegistration->bkash_no,
            'payment_method' => $scholarshipRegistration->payment_method,
            'status' => $scholarshipRegistration->status,
            'status_label' => self::STATUSES[$scholarshipRegistration->status] ?? $scholarshipRegistration->status,
            'created_at' => $scholarshipRegistration->created_at->format('d/m/Y h:i A'),
            'creator_name' => $scholarshipRegistration->creator?->name ?? 'অনলাইন (শিক্ষার্থী)',
        ]);
    }

    public function edit(ScholarshipRegistration $scholarshipRegistration)
    {
        return response()->json([
            'id' => $scholarshipRegistration->id,
            'registration_no' => $scholarshipRegistration->registration_no,
            'student_name' => $scholarshipRegistration->student_name,
            'father_name' => $scholarshipRegistration->father_name,
            'mother_name' => $scholarshipRegistration->mother_name,
            'school_name' => $scholarshipRegistration->school_name,
            'class_no' => (string) $scholarshipRegistration->class_no,
            'roll_no' => $scholarshipRegistration->roll_no,
            'mobile_no' => $scholarshipRegistration->mobile_no,
            'bkash_no' => $scholarshipRegistration->bkash_no,
            'payment_method' => $scholarshipRegistration->payment_method,
            'status' => $scholarshipRegistration->status,
        ]);
    }

    public function update(Request $request, ScholarshipRegistration $scholarshipRegistration): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'class_no' => 'required|in:1,2,3,4,5',
            'roll_no' => 'required|string|max:20',
            'mobile_no' => 'required|regex:/^01[0-9]{9}$/',
            'bkash_no' => 'nullable|regex:/^01[0-9]{9}$/',
            'payment_method' => 'required|in:bkash,cash',
            'status' => 'required|in:pending,approved,rejected',
        ], [
            'student_name.required' => 'শিক্ষার্থীর নাম আবশ্যক।',
            'father_name.required' => 'পিতার নাম আবশ্যক।',
            'mother_name.required' => 'মাতার নাম আবশ্যক।',
            'school_name.required' => 'স্কুলের নাম আবশ্যক।',
            'class_no.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'roll_no.required' => 'রোল নং আবশ্যক।',
            'mobile_no.required' => 'মোবাইল নম্বর আবশ্যক।',
            'mobile_no.regex' => 'সঠিক মোবাইল নম্বর দিন।',
            'bkash_no.regex' => 'সঠিক বিকাশ নম্বর দিন।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম নির্বাচন করুন।',
            'status.required' => 'স্ট্যাটাস নির্বাচন করুন।',
        ]);

        try {
            $validated['class_no'] = (int) $validated['class_no'];
            $validated['bkash_no'] = $validated['payment_method'] === 'cash' ? null : $validated['bkash_no'];

            $scholarshipRegistration->update($validated);

            return redirect()->route('admin.scholarship.index')
                ->with('success', 'রেজিস্ট্রেশন ('.$scholarshipRegistration->registration_no.') সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'রেজিস্ট্রেশন আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function updateStatus(Request $request, ScholarshipRegistration $scholarshipRegistration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $scholarshipRegistration->update(['status' => $validated['status']]);

            $statusLabel = self::STATUSES[$validated['status']];

            return back()->with('success', 'স্ট্যাটাস "'.$statusLabel.'"-এ পরিবর্তন করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', 'স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function print(ScholarshipRegistration $scholarshipRegistration): View
    {
        return view('admin.scholarship.pdf', [
            'registration' => $scholarshipRegistration,
            'classes' => ScholarshipRegistration::CLASSES,
        ]);
    }

    public function downloadAll(Request $request)
    {
        $query = ScholarshipRegistration::query();

        if ($request->filled('class_no')) {
            $query->where('class_no', $request->class_no);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->get();

        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $fontStyle = ['font' => 'Kalpurush', 'size' => 12];
        $boldStyle = ['font' => 'Kalpurush', 'size' => 12, 'bold' => true];
        $headerStyle = ['font' => 'Kalpurush', 'size' => 12, 'bold' => true, 'color' => 'FFFFFF'];
        $titleStyle = ['font' => 'Kalpurush', 'size' => 16, 'bold' => true, 'align' => 'center'];
        $subtitleStyle = ['font' => 'Kalpurush', 'size' => 12, 'align' => 'center'];

        $section->addText('আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি ২০২৬', $titleStyle);
        $section->addText('মেধাবৃত্তি রেজিস্ট্রেশন তালিকা', $subtitleStyle);
        $section->addText('মোট রেজিস্ট্রেশন: '.$registrations->count(), $subtitleStyle);
        $section->addParagraphBreak();

        $tableStyle = new Table;
        $tableStyle->setBorderSize(1);
        $tableStyle->setBorderColor('000000');
        $tableStyle->setWidth(100, 'pct');

        $table = $section->addTable($tableStyle);

        $table->addRow();
        $headers = ['ক্রমিক', 'রেজি নং', 'শিক্ষার্থীর নাম', 'পিতার নাম', 'মাতার নাম', 'স্কুল', 'শ্রেণি', 'রোল', 'মোবাইল', 'পেমেন্ট', 'স্ট্যাটাস', 'তারিখ'];
        foreach ($headers as $header) {
            $table->addCell(0, ['width' => 8, 'widthType' => 'pct', 'bgColor' => '2563EB'])->addText($header, $headerStyle);
        }

        foreach ($registrations as $index => $reg) {
            $table->addRow();
            $table->addCell()->addText((string) ($index + 1), $fontStyle);
            $table->addCell()->addText($reg->registration_no, $fontStyle);
            $table->addCell()->addText($reg->student_name, $fontStyle);
            $table->addCell()->addText($reg->father_name, $fontStyle);
            $table->addCell()->addText($reg->mother_name, $fontStyle);
            $table->addCell()->addText($reg->school_name, $fontStyle);
            $table->addCell()->addText(ScholarshipRegistration::CLASSES[$reg->class_no] ?? '-', $fontStyle);
            $table->addCell()->addText($reg->roll_no ?? '-', $fontStyle);
            $table->addCell()->addText($reg->mobile_no, $fontStyle);
            $table->addCell()->addText($reg->payment_method === 'cash' ? 'ক্যাশ' : 'বিকাশ', $fontStyle);
            $table->addCell()->addText(self::STATUSES[$reg->status] ?? $reg->status, $fontStyle);
            $table->addCell()->addText($reg->created_at->format('d/m/Y'), $fontStyle);
        }

        $fileName = 'scholarship_registrations_'.now()->format('Y-m-d_H-i').'.docx';
        $tempPath = storage_path('app/'.$fileName);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function destroy(ScholarshipRegistration $scholarshipRegistration): RedirectResponse
    {
        try {
            $registrationNo = $scholarshipRegistration->registration_no;
            $scholarshipRegistration->delete();

            return redirect()->route('admin.scholarship.index')
                ->with('success', 'মেধাবৃত্তি রেজিস্ট্রেশন ('.$registrationNo.') মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'রেজিস্ট্রেশন মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
