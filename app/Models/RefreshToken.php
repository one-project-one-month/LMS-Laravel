<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model

{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function isExpired()
    {
        return now()->greaterThan($this->expired_at);
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($token) {
            $token->expired_at = now()->addMinutes(30); // Auto-set expiration time
        });
    }
}
