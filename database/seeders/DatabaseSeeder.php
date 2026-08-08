<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            StoreCategorySeeder::class,
            NursingServiceTypeSeeder::class,
            CareServiceSeeder::class,
            LabCategorySeeder::class,
            XrayCategorySeeder::class,
            DeliveryZoneSeeder::class,
            ForumCategorySeeder::class,
            ArticleSeeder::class,
            ForumPostSeeder::class,
        ]);
    }
}
