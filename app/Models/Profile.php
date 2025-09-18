<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $table = 'table_profiles';

    protected $fillable = [
        'user_id',
        'display_name',
        'occupation',
        'age',
        'gender',
        'status',
        'location'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
