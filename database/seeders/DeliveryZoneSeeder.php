<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'داخل عمان',  'fee' => 2],
            ['name' => 'خارج عمان',  'fee' => 4],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::firstOrCreate(
                ['name' => $zone['name']],
                ['fee' => $zone['fee'], 'is_active' => true]
            );
        }
    }
}
