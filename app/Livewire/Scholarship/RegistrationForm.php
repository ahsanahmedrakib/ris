<?php

namespace App\Livewire\Scholarship;

use App\Models\ScholarshipRegistration;
use App\Notifications\NewSubmission;
use App\Support\UniqueConstraintViolation;
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

    #[Validate(['required', 'regex:/^(01[0-9]{9}|০১[০১২৩৪৫৬৭৮৯]{9})$/u'])]
    public string $mobileNo = '';

    #[Validate(['required_if:paymentMethod,bkash', 'nullable', 'regex:/^(01[0-9]{9}|০১[০১২৩৪৫৬৭৮৯]{9})$/u'])]
    public ?string $bkashNo = null;

    #[Validate('required|in:bkash,cash')]
    public string $paymentMethod = 'bkash';

    public string $registrationNo = '';

    public string $pdfToken = '';

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

        // A second confirm for an already saved form means the same applicant
        // double clicked or replayed the request, which would store a duplicate
        // registration under a fresh number.
        if ($this->saved) {
            return null;
        }

        $attempts = 0;
        $registration = null;

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

                break;
            } catch (QueryException $e) {
                DB::rollBack();
                $attempts++;

                // Only a unique collision is worth retrying: the serial is read
                // then incremented, so a competing submit that won the race is
                // now part of the max and this attempt gets the next number.
                if ($attempts < 3 && UniqueConstraintViolation::matches($e)) {
                    continue;
                }

                report($e);

                return $this->failWithRegistrationError();
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);

                $this->confirming = false;
                $this->dispatch('toast', type: 'error', message: 'একটি অপ্রত্যাশিত সমস্যা হয়েছে। আবার চেষ্টা করুন।');

                return null;
            }
        } while ($attempts < 3);

        if (! $registration instanceof ScholarshipRegistration) {
            return $this->failWithRegistrationError();
        }

        // The registration is committed at this point. A notification failure is
        // a side effect and must never tell the applicant their registration was
        // lost, which previously made them submit again under a new number.
        if (! $this->adminMode) {
            try {
                NewSubmission::sendToAdmins(
                    'scholarship',
                    'নতুন মেধাবৃত্তি রেজিস্ট্রেশন',
                    $registration->student_name.' ('.$registration->mobile_no.') মেধাবৃত্তির জন্য রেজিস্ট্রেশন করেছেন।',
                    route('admin.scholarship.show', $registration),
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->registrationNo = $registration->registration_no;
        $this->pdfToken = $registration->pdf_token;
        $this->confirming = false;
        $this->saved = true;

        if ($this->adminMode) {
            return redirect()->route('admin.scholarship.index')
                ->with('success', 'মেধাবৃত্তি রেজিস্ট্রেশন ('.$registration->registration_no.') সফলভাবে তৈরি হয়েছে।');
        }

        $this->dispatch('toast', type: 'success', message: 'রেজিস্ট্রেশন সফল হয়েছে!');

        return null;
    }

    /**
     * Surface a save failure without pretending the registration was lost, and
     * without leaving the form stuck on its confirmation step.
     */
    private function failWithRegistrationError(): ?RedirectResponse
    {
        $this->confirming = false;
        $this->dispatch('toast', type: 'error', message: 'রেজিস্ট্রেশন সংরক্ষণ করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        $this->addError('classNo', 'রেজিস্ট্রেশন তৈরি করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');

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
            'pdfToken',
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
