<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, LogsActivity, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token_version',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? url(Media::images()->url($this->avatar)) : null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'token_version' => 'integer',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Carries the token version and role into the JWT so a token issued before
     * a password change, role change or deactivation can be rejected on every
     * subsequent request instead of remaining valid until it expires.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
            'is_active' => (bool) $this->is_active,
            'token_version' => (int) $this->token_version,
        ];
    }

    /**
     * Invalidates every previously issued credential for this user.
     *
     * Bumping the version makes outstanding JWTs and web sessions fail the
     * `EnsureUserIsCurrent` check, which is the only way to invalidate a JWT
     * that was signed before the change.
     */
    public function revokeTokens(): void
    {
        $this->forceFill([
            'token_version' => ((int) $this->token_version) + 1,
        ])->save();
    }

    /**
     * Attributes that, when changed, must retire every existing credential.
     */
    private const REVOKING_ATTRIBUTES = ['password', 'role', 'is_active'];

    protected static function booted(): void
    {
        static::updating(function (self $user): void {
            foreach (self::REVOKING_ATTRIBUTES as $attribute) {
                if ($user->isDirty($attribute)) {
                    $user->token_version = ((int) $user->token_version) + 1;

                    return;
                }
            }
        });
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function markedAttendances()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    public function parentStudents()
    {
        return $this->belongsToMany(Student::class, 'student_parents', 'parent_id', 'student_id');
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }
}
