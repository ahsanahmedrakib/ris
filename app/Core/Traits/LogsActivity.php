<?php

namespace App\Core\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    private const ACTION_LABELS = [
        'create' => 'তৈরি হয়েছে',
        'update' => 'আপডেট হয়েছে',
        'delete' => 'মুছে ফেলা হয়েছে',
        'force_delete' => 'স্থায়ীভাবে মুছে ফেলা হয়েছে',
        'restore' => 'পুনরুদ্ধার করা হয়েছে',
    ];

    private const LABEL_ATTRIBUTES = [
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
    ];

    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => self::recordActivity($model, 'create'));
        static::updated(function (Model $model): void {
            $significantChanges = array_diff_key($model->getChanges(), array_flip(['updated_at', 'deleted_at']));

            if ($significantChanges !== []) {
                self::recordActivity($model, 'update');
            }
        });
        static::deleted(function (Model $model): void {
            self::recordActivity($model, $model->isForceDeleting() ? 'force_delete' : 'delete');
        });
        static::restored(fn (Model $model) => self::recordActivity($model, 'restore'));
    }

    protected static function recordActivity(Model $model, string $action): void
    {
        if (Auth::hasUser() === false) {
            return;
        }

        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'description' => self::activityDescription($model, $action),
            'properties' => $action === 'update' ? $model->getChanges() : null,
            'ip_address' => request()->ip(),
        ]);
    }

    protected static function activityDescription(Model $model, string $action): string
    {
        return sprintf('%s: %s', self::ACTION_LABELS[$action] ?? $action, self::resolveLabel($model));
    }

    private static function resolveLabel(Model $model): string
    {
        foreach (self::LABEL_ATTRIBUTES as $attribute) {
            $value = $model->getAttribute($attribute);

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return '#'.$model->getKey();
    }
}
