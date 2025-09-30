<?php

// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'user_package_id',
        'amount',
        'status',
        'method',
        'transaction_ref'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // 🔹 Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userPackage()
    {
        return $this->belongsTo(UserPackage::class);
    }
}

