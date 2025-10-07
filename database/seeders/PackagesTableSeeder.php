<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackagesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('packages')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Send a Real Red Rose',
                'description' => 'Blue eyes, long legs, shorter patience for broke men. Three ex-husbands.',
                'price' => 80.00,
                'duration_days' => 30,
                'benefits' => json_encode(['real_item' => true, 'delivery' => 'included']),
                'icon' => 'rose.png', // e.g. store in /storage/icons/rose.png
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Send as Real Letter',
                'description' => 'Blue eyes, long legs, shorter patience for broke men. Three ex-husbands.',
                'price' => 30.00,
                'duration_days' => 30,
                'benefits' => json_encode(['real_item' => true, 'delivery' => 'included']),
                'icon' => 'envelope-icon.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
