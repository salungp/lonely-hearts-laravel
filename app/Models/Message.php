<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'has_attachment',
        'status',
        'read_at',
    ];

    public function receiver()
    {
        return $this->belongsTo(\App\Models\User::class, 'receiver_id');
    }


    // 🔗 Each message belongs to a conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // 🔗 Sender of the message
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
        
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Ad::class, 'ad_id');
    }
}
