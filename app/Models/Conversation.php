<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'ad_id',
        'author_id',
        'replier_id',
        'progress',
        'unlocked_photo'
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
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function replier()
    {
        return $this->belongsTo(User::class, 'replier_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

