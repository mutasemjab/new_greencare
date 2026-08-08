<?php

namespace Database\Seeders;

use App\Models\CareService;
use Illuminate\Database\Seeder;

class CareServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'رعاية مسنين',          'icon' => 'bi bi-person-heart',   'price' => 20],
            ['name' => 'رعاية مرضى بعد العمليات', 'icon' => 'bi bi-hospital',       'price' => 25],
            ['name' => 'رعاية مرضى الشلل',      'icon' => 'bi bi-person-wheelchair', 'price' => 30],
            ['name' => 'مرافقة طبية',           'icon' => 'bi bi-people-fill',    'price' => 15],
            ['name' => 'رعاية نهارية',          'icon' => 'bi bi-sun-fill',       'price' => 18],
            ['name' => 'رعاية ليلية',           'icon' => 'bi bi-moon-stars-fill', 'price' => 22],
        ];

        foreach ($services as $i => $service) {
            CareService::firstOrCreate(
                ['name' => $service['name']],
                ['icon' => $service['icon'], 'price' => $service['price'], 'sort_order' => $i, 'is_active' => true]
            );
        }
    }
}
