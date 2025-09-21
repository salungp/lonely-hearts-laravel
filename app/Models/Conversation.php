<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'author_id',
        'replier_id',
        'progress',
        'unlocked_photo',
    ];

    // 🔗 One conversation has many messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // 🔗 Conversation belongs to an ad
    // public function ad(): BelongsTo
    // {
    //     return $this->belongsTo(Ad::class, 'ad_id');
    // }

    // 🔗 Author of the ad
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // 🔗 Replier
    public function replier()
    {
        return $this->belongsTo(User::class, 'replier_id');
    }
}
