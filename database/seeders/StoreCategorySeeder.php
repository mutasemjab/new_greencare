<?php

namespace Database\Seeders;

use App\Models\StoreCategory;
use Illuminate\Database\Seeder;

class StoreCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'أدوية',
            'مستلزمات طبية',
            'رعاية الأم والطفل',
            'مستلزمات كبار السن',
            'عناية شخصية',
            'أجهزة طبية',
            'فيتامينات ومكملات',
        ];

        foreach ($categories as $i => $name) {
            StoreCategory::firstOrCreate(
                ['name' => $name],
                ['sort_order' => $i, 'is_active' => true]
            );
        }
    }
}
