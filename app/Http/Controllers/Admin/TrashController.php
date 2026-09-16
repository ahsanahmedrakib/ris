<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TrashController extends Controller
{
    private const TITLE_ATTRIBUTES = [
        'name',
        'title',
        'subject',
        'registration_no',
        'admission_no',
        'username',
        'student_name',
        'student_name_bn',
        'bus_no',
        'isbn',
        'employee_id',
    ];

    private const SEARCH_ATTRIBUTES = [
        'name',
        'title',
        'subject',
        'registration_no',
        'admission_no',
        'username',
        'student_name',
        'student_name_bn',
        'email',
        'phone',
    ];

    public function index(Request $request): View
    {
        $types = config('trashable');
        $selectedType = $request->filled('type') && isset($types[$request->type]) ? $request->type : null;
        $search = $request->filled('search') ? trim($request->search) : null;

        $trashed = collect();

        $configs = $selectedType ? [$selectedType => $types[$selectedType]] : $types;

        foreach ($configs as $key => $config) {
            $modelClass = $config['model'];
            $query = $modelClass::onlyTrashed();

            if (isset($config['role'])) {
                $query->where('role', $config['role']);
            }

            if ($search !== null) {
                $query->where(function ($q) use ($modelClass, $search) {
                    foreach (self::SEARCH_ATTRIBUTES as $attribute) {
                        $instance = new $modelClass;

                        if (Schema::hasColumn($instance->getTable(), $attribute)) {
                            $q->orWhere($attribute, 'like', "%{$search}%");
                        }
                    }
                });
            }

            $query->latest()->limit(500)->get()->each(function (Model $record) use ($key, $config, $trashed): void {
                $trashed->push([
                    'type' => $key,
                    'label' => $config['label'],
                    'table' => (new $config['model'])->getTable(),
                    'record' => $record,
                    'title' => $this->resolveTitle($record),
                ]);
            });
        }

        $trashed = $trashed
            ->sortByDesc(fn (array $item) => $item['record']->deleted_at?->timestamp ?? 0)
            ->values();

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $trashed->forPage($page, $perPage),
            $trashed->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginator->appends($request->query());

        return view('admin.trash.index', [
            'trashed' => $paginator,
            'types' => array_map(fn (array $config) => $config['label'], $types),
            'totalCount' => $trashed->count(),
            'breadcrumbs' => ['ট্র্যাশ' => null],
        ]);
    }

    public function restore(Request $request, string $type, int $id): RedirectResponse
    {
        $config = $this->typeConfig($type);
        $modelClass = $config['model'];

        $record = $modelClass::onlyTrashed()->findOrFail($id);

        $record->restore();

        if ($record instanceof User && $record->role === 'teacher') {
            $record->teacherProfile()?->withTrashed()?->first()?->restore();
            $record->update(['is_active' => true]);
        } elseif ($record instanceof Student) {
            $record->user()?->update(['is_active' => true]);
        }

        return back()->with('success', 'ট্র্যাশ থেকে সফলভাবে পুনরুদ্ধার করা হয়েছে।');
    }

    public function forceDelete(Request $request, string $type, int $id): RedirectResponse
    {
        $config = $this->typeConfig($type);
        $modelClass = $config['model'];

        $record = $modelClass::onlyTrashed()->findOrFail($id);

        if ($record instanceof Admission && $record->student_photo) {
            Storage::disk('public')->delete($record->student_photo);
        }

        if ($record instanceof TeacherProfile && $record->photo) {
            Storage::disk('public')->delete($record->photo);
        }

        if ($record instanceof User && $record->role === 'teacher') {
            $record->teacherProfile()?->withTrashed()?->first()?->forceDelete();
        }

        $record->forceDelete();

        return back()->with('success', 'ডেটা স্থায়ীভাবে মুছে ফেলা হয়েছে।');
    }

    private function typeConfig(string $type): array
    {
        $typeConfig = config("trashable.{$type}");

        abort_unless(is_array($typeConfig), 404);

        return $typeConfig;
    }

    private function resolveTitle(Model $record): string
    {
        foreach (self::TITLE_ATTRIBUTES as $attribute) {
            $value = $record->getAttribute($attribute);

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return '#'.$record->getKey();
    }
}
