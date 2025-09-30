<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Photo extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ad_id',
        'file_path',
        'sort_order',
        'is_primary',
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

    // relation to Ad
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
