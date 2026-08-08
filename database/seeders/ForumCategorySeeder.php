<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumSubCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'الحمل والولادة' => ['متابعة الحمل', 'ما بعد الولادة'],
            'رعاية الأطفال'  => ['التغذية', 'اللقاحات', 'النوم'],
            'الصحة العامة'   => ['التغذية', 'اللياقة', 'الأمراض المزمنة'],
            'كبار السن'      => ['الرعاية اليومية', 'الأدوية'],
        ];

        $i = 0;
        foreach ($categories as $categoryName => $subCategories) {
            $category = ForumCategory::firstOrCreate(
                ['name' => $categoryName],
                ['sort_order' => $i++, 'is_active' => true]
            );

            foreach ($subCategories as $j => $subName) {
                ForumSubCategory::firstOrCreate(
                    ['forum_category_id' => $category->id, 'name' => $subName],
                    ['sort_order' => $j, 'is_active' => true]
                );
            }
        }
    }
}
