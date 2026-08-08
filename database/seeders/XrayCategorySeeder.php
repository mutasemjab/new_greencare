<?php

namespace Database\Seeders;

use App\Models\XrayCategory;
use App\Models\XrayTest;
use Illuminate\Database\Seeder;

class XrayCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'أشعة سينية' => [
                ['name' => 'أشعة على الصدر',    'price' => 20],
                ['name' => 'أشعة على العظام',   'price' => 20],
            ],
            'أشعة مقطعية' => [
                ['name' => 'أشعة مقطعية على الدماغ', 'price' => 60],
                ['name' => 'أشعة مقطعية على البطن',   'price' => 65],
            ],
            'أشعة صوتية' => [
                ['name' => 'إيكو على القلب',     'price' => 35],
                ['name' => 'سونار على البطن',     'price' => 30],
            ],
        ];

        $i = 0;
        foreach ($categories as $categoryName => $tests) {
            $category = XrayCategory::firstOrCreate(
                ['name' => $categoryName],
                ['sort_order' => $i++, 'is_active' => true]
            );

            foreach ($tests as $test) {
                XrayTest::firstOrCreate(
                    ['xray_category_id' => $category->id, 'name' => $test['name']],
                    ['price' => $test['price'], 'is_active' => true]
                );
            }
        }
    }
}
