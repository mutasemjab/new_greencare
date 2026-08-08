<?php

namespace Database\Seeders;

use App\Models\LabCategory;
use App\Models\LabTest;
use Illuminate\Database\Seeder;

class LabCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'تحاليل الدم' => [
                ['name' => 'صورة دم شاملة (CBC)', 'price' => 10],
                ['name' => 'سكر تراكمي (HbA1c)',   'price' => 12],
                ['name' => 'وظائف كبد',            'price' => 15],
                ['name' => 'وظائف كلى',            'price' => 15],
            ],
            'تحاليل البول' => [
                ['name' => 'تحليل بول عام',   'price' => 6],
                ['name' => 'زراعة بول',       'price' => 12],
            ],
            'تحاليل هرمونات' => [
                ['name' => 'الغدة الدرقية (TSH)', 'price' => 14],
                ['name' => 'هرمون الحليب',        'price' => 14],
            ],
        ];

        $i = 0;
        foreach ($categories as $categoryName => $tests) {
            $category = LabCategory::firstOrCreate(
                ['name' => $categoryName],
                ['sort_order' => $i++, 'is_active' => true]
            );

            foreach ($tests as $test) {
                LabTest::firstOrCreate(
                    ['lab_category_id' => $category->id, 'name' => $test['name']],
                    ['price' => $test['price'], 'is_active' => true]
                );
            }
        }
    }
}
