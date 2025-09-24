<?php

// app/Models/Package.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'duration_days', 'benefits',
    ];

    protected $casts = [
        'benefits' => 'array',
    ];

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
