<?php

namespace Database\Seeders;

use App\Models\NursingServiceType;
use Illuminate\Database\Seeder;

class NursingServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'حقن وريدي',        'icon' => 'bi bi-droplet-fill',   'price' => 8],
            ['name' => 'تركيب محلول',       'icon' => 'bi bi-clipboard2-pulse', 'price' => 15],
            ['name' => 'تغيير ضمادات',      'icon' => 'bi bi-bandaid',        'price' => 10],
            ['name' => 'قياس الضغط والسكري', 'icon' => 'bi bi-heart-pulse',    'price' => 5],
            ['name' => 'تركيب قسطرة',       'icon' => 'bi bi-syringe',        'price' => 12],
            ['name' => 'رعاية جروح',        'icon' => 'bi bi-bandaid-fill',   'price' => 15],
        ];

        foreach ($types as $i => $type) {
            NursingServiceType::firstOrCreate(
                ['name' => $type['name']],
                ['icon' => $type['icon'], 'price' => $type['price'], 'sort_order' => $i, 'is_active' => true]
            );
        }
    }
}
