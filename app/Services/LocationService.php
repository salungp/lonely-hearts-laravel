<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    /**
     * Detect location from IP, cached for 1 day
     */
    public function detect(string $ip = null): array
    {
        $ip = $ip ?? request()->ip();

        // Handle localhost
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '8.8.8.8'; // fallback for dev
        }

        $response = Http::get("https://ipapi.co/{$ip}/json/");

        if ($response->failed()) {
            return [
                'ip'      => $ip,
                'country' => 'Unknown',
                'city'    => 'Unknown',
                'lat'     => null,
                'lon'     => null,
            ];
        }

        $data = $response->json();

        return [
            'ip'      => $ip,
            'country' => $data['country_name'] ?? 'Unknown',
            'city'    => $data['city'] ?? 'Unknown',
            'lat'     => $data['latitude'] ?? null,
            'lon'     => $data['longitude'] ?? null,
        ];
    }
}
