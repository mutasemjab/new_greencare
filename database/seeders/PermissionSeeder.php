<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // الأدوار والموظفين
            'role-table', 'role-add', 'role-edit', 'role-delete',
            'employee-table', 'employee-add', 'employee-edit', 'employee-delete',

            // المستخدمون
            'user-table', 'user-add', 'user-edit', 'user-delete',

            // البنرات
            'banner-table', 'banner-add', 'banner-edit', 'banner-delete',

            // تصنيفات المتجر
            'store-category-table', 'store-category-add', 'store-category-edit', 'store-category-delete',

            // المنتجات
            'product-table', 'product-add', 'product-edit', 'product-delete',

            // الطلبات
            'order-table', 'order-edit',

            // مناطق التوصيل
            'delivery-zone-table', 'delivery-zone-add', 'delivery-zone-edit', 'delivery-zone-delete',

            // الأطباء والحجوزات
            'doctor-table', 'doctor-add', 'doctor-edit', 'doctor-delete',
            'doctor-booking-table',

            // التمريض
            'nursing-type-table', 'nursing-type-add', 'nursing-type-edit', 'nursing-type-delete',
            'nursing-request-table', 'nursing-request-edit',

            // الاستحمام
            'bathing-table', 'bathing-add', 'bathing-edit', 'bathing-delete',
            'bathing-request-table', 'bathing-request-edit',

            // خدمات الرعاية
            'care-service-table', 'care-service-add', 'care-service-edit', 'care-service-delete',
            'care-request-table', 'care-request-edit',

            // المختبر
            'lab-category-table', 'lab-category-add', 'lab-category-edit', 'lab-category-delete',
            'lab-test-table', 'lab-test-add', 'lab-test-edit', 'lab-test-delete',
            'lab-request-table', 'lab-request-edit',
            'lab-staff-table', 'lab-staff-add', 'lab-staff-edit', 'lab-staff-delete',

            // الأشعة
            'xray-category-table', 'xray-category-add', 'xray-category-edit', 'xray-category-delete',
            'xray-test-table', 'xray-test-add', 'xray-test-edit', 'xray-test-delete',
            'xray-request-table', 'xray-request-edit',

            // المقالات
            'article-table', 'article-add', 'article-edit', 'article-delete',

            // المنتدى
            'forum-category-table', 'forum-category-add', 'forum-category-edit', 'forum-category-delete',
            'forum-post-table', 'forum-post-edit', 'forum-post-delete',

            // الإشعارات
            'notification-table', 'notification-add',
            'fcm-send',

            // الغرف
            'room-table', 'room-add', 'room-edit', 'room-delete',

            // قوالب التقارير
            'template-table', 'template-add', 'template-edit', 'template-delete',

            // التشخيصات والأمراض المزمنة
            'diagnosis-table', 'diagnosis-add', 'diagnosis-edit', 'diagnosis-delete',
            'chronic-disease-table', 'chronic-disease-add', 'chronic-disease-edit', 'chronic-disease-delete',

            // الشكاوى
            'complaint-table', 'complaint-edit',

            // نماذج الزيارة
            'visit-form-table',
            'visit-form-field-table', 'visit-form-field-add', 'visit-form-field-edit', 'visit-form-field-delete',

            // قوالب الوثائق
            'document-template-table', 'document-template-edit',

            // الأدوية
            'medication-table',

            // ملاحظات النقل
            'display-note-table', 'display-note-add', 'display-note-edit', 'display-note-delete',

            // نقل المرضى
            'transfer-table', 'transfer-edit',

            // التغذية
            'nutrition-table', 'nutrition-edit',

            // الإعدادات
            'setting-edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
