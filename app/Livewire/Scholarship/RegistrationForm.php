<?php

namespace App\Livewire\Scholarship;

use App\Models\ScholarshipRegistration;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RegistrationForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $studentName = '';

    #[Validate('required|string|max:255')]
    public string $fatherName = '';

    #[Validate('required|string|max:255')]
    public string $motherName = '';

    #[Validate('required|string|max:255')]
    public string $schoolName = '';

    #[Validate('required|in:1,2,3,4,5')]
    public ?int $classNo = null;

    #[Validate('required|string|max:20')]
    public ?string $rollNo = null;

    #[Validate('required|regex:/^01[0-9]{9}$/')]
    public string $mobileNo = '';

    #[Validate('required_if:paymentMethod,bkash|regex:/^01[0-9]{9}$/')]
    public ?string $bkashNo = null;

    #[Validate('required|in:bkash,cash')]
    public string $paymentMethod = 'bkash';

    public string $registrationNo = '';

    public bool $confirming = false;

    public bool $saved = false;

    public bool $adminMode = false;

    public function mount(bool $adminMode = false): void
    {
        $this->adminMode = $adminMode;
    }

    public function updatedClassNo(): void
    {
        $this->confirming = false;

        $this->registrationNo = $this->classNo
            ? ScholarshipRegistration::nextRegistrationNo((int) $this->classNo)
            : '';
    }

    public function updatedPaymentMethod(): void
    {
        $this->confirming = false;

        if ($this->paymentMethod === 'cash') {
            $this->resetValidation('bkashNo');
        }
    }

    public function submit(): void
    {
        $this->validate();

        $this->confirming = true;
    }

    public function cancelConfirmation(): void
    {
        $this->confirming = false;
    }

    public function confirm(): ?RedirectResponse
    {
        $this->validate();

        $attempts = 0;

        do {
            try {
                DB::beginTransaction();

                $number = ScholarshipRegistration::nextRegistrationNo((int) $this->classNo);

                $registration = ScholarshipRegistration::create([
                    'registration_no' => $number,
                    'serial_no' => (int) substr($number, -3),
                    'student_name' => $this->studentName,
                    'father_name' => $this->fatherName,
                    'mother_name' => $this->motherName,
                    'school_name' => $this->schoolName,
                    'class_no' => (int) $this->classNo,
                    'roll_no' => $this->rollNo,
                    'mobile_no' => $this->mobileNo,
                    'bkash_no' => $this->paymentMethod === 'cash' ? null : $this->bkashNo,
                    'payment_method' => $this->paymentMethod,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);

                DB::commit();

                $this->registrationNo = $registration->registration_no;
                $this->confirming = false;
                $this->saved = true;

                if ($this->adminMode) {
                    return redirect()->route('admin.scholarship.index')
                        ->with('success', 'মেধাবৃত্তি রেজিস্ট্রেশন ('.$registration->registration_no.') সফলভাবে তৈরি হয়েছে।');
                }

                return null;
            } catch (QueryException $e) {
                DB::rollBack();

                $attempts++;

                if (! str_contains(strtolower($e->getMessage()), 'unique')) {
                    throw $e;
                }
            }
        } while ($attempts < 3);

        $this->addError('class_no', 'রেজিস্ট্রেশন তৈরি করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');

        return null;
    }

    public function resetForm(): void
    {
        $this->reset([
            'studentName',
            'fatherName',
            'motherName',
            'schoolName',
            'classNo',
            'rollNo',
            'mobileNo',
            'bkashNo',
            'paymentMethod',
            'registrationNo',
            'confirming',
        ]);

        $this->saved = false;
        $this->resetValidation();
    }

    protected function messages(): array
    {
        return [
            'studentName.required' => 'শিক্ষার্থীর নাম আবশ্যক।',
            'studentName.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'fatherName.required' => 'পিতার নাম আবশ্যক।',
            'fatherName.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'motherName.required' => 'মাতার নাম আবশ্যক।',
            'motherName.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'schoolName.required' => 'স্কুলের নাম আবশ্যক।',
            'schoolName.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'classNo.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'classNo.in' => 'সঠিক শ্রেণি নির্বাচন করুন।',
            'rollNo.required' => 'রোল নম্বর আবশ্যক।',
            'rollNo.max' => 'রোল নম্বর ২০ অক্ষরের বেশি হতে পারবে না।',
            'mobileNo.required' => 'মোবাইল নম্বর আবশ্যক।',
            'mobileNo.regex' => 'সঠিক মোবাইল নম্বর দিন (০১ দিয়ে শুরু ১১ সংখ্যা)।',
            'bkashNo.required_if' => 'বিকাশ নম্বর আবশ্যক।',
            'bkashNo.regex' => 'সঠিক বিকাশ নম্বর দিন (০১ দিয়ে শুরু ১১ সংখ্যা)।',
            'paymentMethod.required' => 'পেমেন্ট মাধ্যম নির্বাচন করুন।',
            'paymentMethod.in' => 'সঠিক পেমেন্ট মাধ্যম নির্বাচন করুন।',
        ];
    }

    public function render(): View
    {
        return view('livewire.scholarship.registration-form', [
            'classOptions' => ScholarshipRegistration::CLASSES,
            'bkashNumber' => ScholarshipRegistration::BKASH_NUMBER,
        ]);
    }
}
