<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'attempts',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasAttemptsLeft(int $max = 5): bool
    {
        return $this->attempts < $max;
    }
}
