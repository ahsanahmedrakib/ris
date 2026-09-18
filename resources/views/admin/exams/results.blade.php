@extends('layouts.admin')

@section('title', 'পরীক্ষার ফলাফল প্রবেশ করুন')

@section('content')
<div class="space-y-6" x-data="resultSheetApp()">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.exams.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">পরীক্ষার ফলাফল প্রবেশ করুন</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $exam->name ?? '' }} <span class="text-gray-400">•</span> {{ $exam->classRoom?->name ?? '' }} <span class="text-gray-400">•</span> মোট {{ $exam->total_marks }} নম্বর, পাস {{ $exam->passing_marks }}</p>
        </div>
    </div>

    <div class="bg-ris-primary/5 border border-ris-primary/10 rounded-lg px-4 py-3 text-sm text-ris-dark">
        <span class="font-medium">পরামর্শ:</span> শুধুমাত্র যেসব ঘরে নম্বর দেওয়া হয়েছে সেগুলোই সংরক্ষণ হবে। এখানে আবার নম্বর পরিবর্তন করে সংরক্ষণ করলে পুরনো ফলাফল আপডেট হবে।
    </div>

    <form method="POST" action="{{ route('admin.exams.results.store', $exam) }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-heading font-semibold text-gray-900">ছাত্রদের নম্বর প্রবেশ করুন</h2>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    ফলাফল সংরক্ষণ করুন
                </button>
            </div>

            @if (($students ?? [])->isEmpty() || ($subjects ?? [])->isEmpty())
                <div class="px-5 py-14 text-center text-gray-400 text-sm">
                    @if (($students ?? [])->isEmpty())
                        এই শ্রেণিতে সক্রিয় কোনো শিক্ষার্থী নেই।
                    @else
                        এই শ্রেণিতে কোনো বিষয় যোগ করা হয়নি। আগে বিষয় যোগ করুন।
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                                @foreach($subjects as $subject)
                                    <th class="text-center px-3 py-3.5 font-medium text-gray-500 min-w-[110px]">
                                        {{ $subject->name }}
                                        <span class="block text-xs font-normal text-gray-400">(০-{{ $exam->total_marks }})</span>
                                    </th>
                                @endforeach
                                <th class="text-center px-3 py-3.5 font-medium text-gray-500">মোট</th>
                                <th class="text-center px-3 py-3.5 font-medium text-gray-500">গ্রেড</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                                {{ substr($student->user?->name ?? $student->admission_no, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $student->user?->name ?? $student->admission_no }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $student->roll_no }}</td>
                                    @foreach($subjects as $subject)
                                        <td class="px-3 py-3 text-center">
                                            <input type="number"
                                                   name="result[{{ $student->id }}][{{ $subject->id }}]"
                                                   x-model="marks[{{ $student->id }}][{{ $subject->id }}]"
                                                   min="0" max="{{ $exam->total_marks }}"
                                                   class="w-20 px-2 py-1.5 border border-gray-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                                   placeholder="0">
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-3 text-center font-medium text-gray-900" x-text="studentTotal({{ $student->id }})">-</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              :class="{ 'bg-red-50 text-red-700': studentGrade({{ $student->id }}) === 'F', 'bg-emerald-50 text-emerald-700': studentGrade({{ $student->id }}) !== 'F' && studentGrade({{ $student->id }}) !== '-' }"
                                              x-text="studentGrade({{ $student->id }})">-</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </form>

</div>
@endsection

@section('scripts')
    <script>
        function resultSheetApp() {
            return {
                totalMarks: {{ $exam->total_marks }},
                passingMarks: {{ $exam->passing_marks }},
                marks: {!! Js::from($marks) !!},

                gradeOf(marks) {
                    const value = Number(marks);
                    if (marks === '' || marks === null || marks === undefined || !Number.isFinite(value)) {
                        return '-';
                    }
                    if (value < this.passingMarks) {
                        return 'F';
                    }
                    const percentage = (value / this.totalMarks) * 100;
                    if (percentage >= 80) return 'A+';
                    if (percentage >= 70) return 'A';
                    if (percentage >= 60) return 'A-';
                    if (percentage >= 50) return 'B';
                    if (percentage >= 40) return 'C';
                    return 'D';
                },

                studentTotal(studentId) {
                    const subjectMarks = this.marks[studentId] || {};
                    return Object.values(subjectMarks).reduce((sum, value) => sum + (Number(value) || 0), 0);
                },

                studentGrade(studentId) {
                    const subjectMarks = this.marks[studentId] || {};
                    const subjects = Object.keys(subjectMarks);
                    if (!subjects.length) return '-';

                    let sum = 0;
                    let count = 0;
                    subjects.forEach((subjectId) => {
                        const grade = this.gradeOf(subjectMarks[subjectId]);
                        if (grade !== '-') {
                            sum += Number(subjectMarks[subjectId]);
                            count++;
                        }
                    });

                    if (!count) return '-';

                    const average = sum / count;
                    if (average < this.passingMarks) return 'F';

                    const percentage = (sum / (count * this.totalMarks)) * 100;
                    if (percentage >= 80) return 'A+';
                    if (percentage >= 70) return 'A';
                    if (percentage >= 60) return 'A-';
                    if (percentage >= 50) return 'B';
                    if (percentage >= 40) return 'C';
                    return 'D';
                }
            };
        }
    </script>
@endsection