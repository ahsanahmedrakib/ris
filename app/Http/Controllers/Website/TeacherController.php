<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\JsonLd;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->with('teacherProfile')
            ->whereHas('teacherProfile')
            ->orderByRaw("(SELECT CASE designation WHEN 'প্রধান শিক্ষক' THEN 1 WHEN 'সহকারী প্রধান শিক্ষক' THEN 2 WHEN 'সহকারী শিক্ষক' THEN 3 ELSE 4 END FROM teacher_profiles WHERE user_id = users.id LIMIT 1)")
            ->get();

        $seo = Seo::forCurrentPage([
            'schema' => [
                JsonLd::collectionPage(
                    route('teachers'),
                    'Our Teachers — Resma International School, Gopalganj',
                    'The teaching staff of Resma International School, Gopalganj, with qualifications and subjects taught.',
                    $teachers->map(fn (User $teacher): string => route('teacher.single', $teacher->teacherProfile->slug))->all(),
                ),
            ],
        ])->withCanonical(route('teachers'));

        return view('website.teachers.index', compact('teachers', 'seo'));
    }

    public function single(string $slug): View
    {
        $teacher = User::where('role', 'teacher')
            ->where('is_active', true)
            ->whereHas('teacherProfile', function ($q) use ($slug) {
                $q->where('slug', $slug)->where('is_active', true);
            })
            ->with('teacherProfile')
            ->firstOrFail();

        $profile = $teacher->teacherProfile;

        $description = Seo::excerpt($profile?->bio, 160);

        if ($description === '') {
            $description = 'Profile of '.$teacher->name
                .($profile?->designation ? ', '.$profile->designation : '')
                .', teaching at '.Seo::siteName().', Gopalganj.'
                .($profile?->subject ? ' Teaches '.$profile->subject.'.' : '');
        }

        $seo = Seo::forCurrentPage([
            'title' => Seo::composeTitle($teacher->name.($profile?->designation ? ', '.$profile->designation : '')),
            'description' => $description,
            'image' => $profile?->photo ? Media::images()->url($profile->photo) : null,
            'schema' => [$this->personNode($teacher)],
        ])->andCrumb(['name' => $teacher->name]);

        return view('website.teachers.single', compact('teacher', 'seo'));
    }

    /**
     * A Person node for one teacher.
     *
     * Staff pages are the ones most likely to be searched by a parent looking
     * for a specific teacher by name, so the name and role are stated plainly
     * rather than left for a crawler to infer from the heading.
     *
     * @return array<string, mixed>
     */
    protected function personNode(User $teacher): array
    {
        $profile = $teacher->teacherProfile;

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $teacher->name,
            'jobTitle' => $profile?->designation,
            'worksFor' => ['@id' => url('/').'#school'],
            'knowsAbout' => $profile?->subject,
            'image' => $profile?->photo ? Media::images()->url($profile->photo) : null,
            'description' => Seo::excerpt($profile?->bio, 300),
        ], fn ($value): bool => $value !== null && $value !== '');
    }
}
