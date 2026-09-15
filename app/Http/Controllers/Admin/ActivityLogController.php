<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    private const ACTION_OPTIONS = [
        'create' => 'তৈরি',
        'update' => 'আপডেট',
        'delete' => 'মুছে ফেলা',
        'force_delete' => 'স্থায়ীভাবে মুছে ফেলা',
        'restore' => 'পুনরুদ্ধার',
    ];

    public function index(Request $request): View
    {
        $query = ActivityLog::query()->with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $modelByKey = collect(config('trashable'))->mapWithKeys(fn (array $cfg, string $key) => [$key => $cfg['model']]);

            if ($model = $modelByKey->get($request->model)) {
                $query->where('subject_type', $model);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $logs = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'actions' => self::ACTION_OPTIONS,
            'models' => collect(config('trashable'))
                ->mapWithKeys(fn (array $cfg, string $key) => [$key => $cfg['label']])
                ->sort()
                ->all(),
            'breadcrumbs' => ['অ্যাক্টিভিটি লগ' => null],
        ]);
    }
}
