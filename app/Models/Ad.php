<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    protected $table = 'ads'; // just to be explicit

    protected $fillable = [
        'user_id',
        'description',
        'slug',
        'location',
        'box_number',
        'views',
        'snapshot_age',
        'snapshot_name',
        'snapshot_occupation',
        'snapshot_status',
        'snapshot_gender',
    ];

    // 🔹 Ad belongs to a user (the author)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Ad has many conversations
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
