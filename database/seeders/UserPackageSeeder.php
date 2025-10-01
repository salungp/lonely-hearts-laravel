<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Package;
use App\Models\UserPackage;
use Carbon\Carbon;

class UserPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Grab any existing user and package
        $user    = User::first();
        $package = Package::inRandomOrder()->first(); // ✅ pick any available package

        // Safety check
        if (!$user || !$package) {
            $this->command->warn('⚠️ No users or packages found. Run UserSeeder and PackageSeeder first.');
            return;
        }

        // Create a featured package subscription for this user
        UserPackage::create([
            'user_id'    => $user->id,
            'package_id' => $package->id,
            'start_date' => Carbon::now(),
            'end_date'   => Carbon::now()->addDays($package->duration_days),
            'status'     => 'active',
        ]);
    }
}
