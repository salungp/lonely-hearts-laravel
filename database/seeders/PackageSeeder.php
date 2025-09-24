<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Featured',
            'description' => 'Your ad will appear higher and highlighted to capture more attention.',
            'price' => 20.00,
            'duration_days' => 30, // package valid for 30 days
            'benefits' => [
                'highlight_ad' => true,
                'priority' => 'high',
            ],
        ]);
    }
}
