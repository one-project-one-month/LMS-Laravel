<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $guarded = [];

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function refresh_token()
    {
        return $this->hasOne(RefreshToken::class);
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('role', fn($query) => $query->where('role', 'admin'));
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('role', fn($query) => $query->where('role', 'student'));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected static function booted(): void
    {
        static::created(function (User $user) {
            
            $refresh_token = Str::random(16);
            $user->refresh_token()->create([
                'refresh_token' => $refresh_token,
            ]);
        });
    }
}
