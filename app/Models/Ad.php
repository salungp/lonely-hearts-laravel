<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ad extends Model
{
    protected $table = 'ads'; // just to be explicit

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'location',
        'box_number', 'views', 'snapshot_age', 'snapshot_name',
        'snapshot_occupation', 'snapshot_status', 'snapshot_gender'
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

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Ad has many conversations
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function adOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
