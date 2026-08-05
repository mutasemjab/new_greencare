<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BathingController;
use App\Http\Controllers\Admin\CareController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ForumController;
use App\Http\Controllers\Admin\LabController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MedicationController;
use App\Http\Controllers\Admin\NursingController;
use App\Http\Controllers\Admin\NutritionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PatientTransferController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StoreCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\XrayController;

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {

        // ── Dashboard ─────────────────────────────────────────────────
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');

        // ── Admin profile ─────────────────────────────────────────────
        Route::get('/admin/edit/{id}',    [LoginController::class, 'editlogin'])->name('admin.login.edit');
        Route::post('/admin/update/{id}', [LoginController::class, 'updatelogin'])->name('admin.login.update');

        // ── Roles & Employees ─────────────────────────────────────────
        Route::resource('employee', EmployeeController::class, ['as' => 'admin']);
        Route::get('role',               [RoleController::class, 'index'])->name('admin.role.index');
        Route::get('role/create',        [RoleController::class, 'create'])->name('admin.role.create');
        Route::get('role/{id}/edit',     [RoleController::class, 'edit'])->name('admin.role.edit');
        Route::patch('role/{id}',        [RoleController::class, 'update'])->name('admin.role.update');
        Route::post('role',              [RoleController::class, 'store'])->name('admin.role.store');
        Route::post('admin/role/delete', [RoleController::class, 'delete'])->name('admin.role.delete');

        Route::get('/permissions/{guard_name}', function ($guard_name) {
            return response()->json(Permission::where('guard_name', $guard_name)->get());
        });

        // ── Users ─────────────────────────────────────────────────────────────
        Route::resource('users', UserController::class, ['as' => 'admin']);

        // ── Banners ───────────────────────────────────────────────────────────
        Route::resource('banners', BannerController::class, ['as' => 'admin']);

        // ── Store ─────────────────────────────────────────────────────────────
        Route::resource('store/categories', StoreCategoryController::class, [
            'as'         => 'admin.store',
            'parameters' => ['categories' => 'storeCategory'],
        ]);
        Route::resource('store/products', ProductController::class, [
            'as'         => 'admin.store',
            'parameters' => ['products' => 'product'],
        ]);

        // ── Delivery Zones ────────────────────────────────────────────────────
        Route::resource('delivery-zones', DeliveryZoneController::class, [
            'as'         => 'admin',
            'parameters' => ['delivery-zones' => 'deliveryZone'],
        ]);

        // ── Orders ────────────────────────────────────────────────────────────
        Route::get('orders',                   [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('orders/{order}',           [OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('orders/{order}/status',  [OrderController::class, 'updateStatus'])->name('admin.orders.status');

        // ── Nursing ───────────────────────────────────────────────────────────
        Route::get('nursing/types',                            [NursingController::class, 'types'])->name('admin.nursing.types');
        Route::get('nursing/types/create',                     [NursingController::class, 'createType'])->name('admin.nursing.types.create');
        Route::post('nursing/types',                           [NursingController::class, 'storeType'])->name('admin.nursing.types.store');
        Route::get('nursing/types/{type}/edit',                [NursingController::class, 'editType'])->name('admin.nursing.types.edit');
        Route::patch('nursing/types/{type}',                   [NursingController::class, 'updateType'])->name('admin.nursing.types.update');
        Route::delete('nursing/types/{type}',                  [NursingController::class, 'destroyType'])->name('admin.nursing.types.destroy');
        Route::get('nursing/requests',                         [NursingController::class, 'requests'])->name('admin.nursing.requests');
        Route::get('nursing/requests/{request}',               [NursingController::class, 'showRequest'])->name('admin.nursing.requests.show');
        Route::patch('nursing/requests/{request}/status',      [NursingController::class, 'updateRequestStatus'])->name('admin.nursing.requests.status');

        // ── Bathing ───────────────────────────────────────────────────────────
        Route::get('bathing/pos',                              [BathingController::class, 'pointsOfSale'])->name('admin.bathing.pos');
        Route::get('bathing/pos/create',                       [BathingController::class, 'createPoint'])->name('admin.bathing.pos.create');
        Route::post('bathing/pos',                             [BathingController::class, 'storePoint'])->name('admin.bathing.pos.store');
        Route::get('bathing/pos/{point}/edit',                 [BathingController::class, 'editPoint'])->name('admin.bathing.pos.edit');
        Route::patch('bathing/pos/{point}',                    [BathingController::class, 'updatePoint'])->name('admin.bathing.pos.update');
        Route::delete('bathing/pos/{point}',                   [BathingController::class, 'destroyPoint'])->name('admin.bathing.pos.destroy');
        Route::get('bathing/cards',                            [BathingController::class, 'cards'])->name('admin.bathing.cards');
        Route::get('bathing/cards/generate',                   [BathingController::class, 'generateCardsForm'])->name('admin.bathing.cards.generate');
        Route::post('bathing/cards/generate',                  [BathingController::class, 'generateCards'])->name('admin.bathing.cards.generate.store');
        Route::delete('bathing/cards/{card}',                  [BathingController::class, 'destroyCard'])->name('admin.bathing.cards.destroy');
        Route::get('bathing/requests',                         [BathingController::class, 'requests'])->name('admin.bathing.requests');
        Route::get('bathing/requests/{request}',               [BathingController::class, 'showRequest'])->name('admin.bathing.requests.show');
        Route::patch('bathing/requests/{request}/status',      [BathingController::class, 'updateRequestStatus'])->name('admin.bathing.requests.status');

        // ── Care ──────────────────────────────────────────────────────────────
        Route::get('care/services',                            [CareController::class, 'services'])->name('admin.care.services');
        Route::get('care/services/create',                     [CareController::class, 'createService'])->name('admin.care.services.create');
        Route::post('care/services',                           [CareController::class, 'storeService'])->name('admin.care.services.store');
        Route::get('care/services/{service}/edit',             [CareController::class, 'editService'])->name('admin.care.services.edit');
        Route::patch('care/services/{service}',                [CareController::class, 'updateService'])->name('admin.care.services.update');
        Route::delete('care/services/{service}',               [CareController::class, 'destroyService'])->name('admin.care.services.destroy');
        Route::get('care/requests',                            [CareController::class, 'requests'])->name('admin.care.requests');
        Route::get('care/requests/{request}',                  [CareController::class, 'showRequest'])->name('admin.care.requests.show');
        Route::patch('care/requests/{request}/status',         [CareController::class, 'updateRequestStatus'])->name('admin.care.requests.status');

        // ── Lab ───────────────────────────────────────────────────────────────
        Route::get('lab/categories',                           [LabController::class, 'categories'])->name('admin.lab.categories');
        Route::get('lab/categories/create',                    [LabController::class, 'createCategory'])->name('admin.lab.categories.create');
        Route::post('lab/categories',                          [LabController::class, 'storeCategory'])->name('admin.lab.categories.store');
        Route::get('lab/categories/{category}/edit',           [LabController::class, 'editCategory'])->name('admin.lab.categories.edit');
        Route::patch('lab/categories/{category}',              [LabController::class, 'updateCategory'])->name('admin.lab.categories.update');
        Route::delete('lab/categories/{category}',             [LabController::class, 'destroyCategory'])->name('admin.lab.categories.destroy');
        Route::get('lab/tests',                                [LabController::class, 'tests'])->name('admin.lab.tests');
        Route::get('lab/tests/create',                         [LabController::class, 'createTest'])->name('admin.lab.tests.create');
        Route::post('lab/tests',                               [LabController::class, 'storeTest'])->name('admin.lab.tests.store');
        Route::get('lab/tests/{test}/edit',                    [LabController::class, 'editTest'])->name('admin.lab.tests.edit');
        Route::patch('lab/tests/{test}',                       [LabController::class, 'updateTest'])->name('admin.lab.tests.update');
        Route::delete('lab/tests/{test}',                      [LabController::class, 'destroyTest'])->name('admin.lab.tests.destroy');
        Route::get('lab/requests',                             [LabController::class, 'requests'])->name('admin.lab.requests');
        Route::get('lab/requests/{request}',                   [LabController::class, 'showRequest'])->name('admin.lab.requests.show');
        Route::patch('lab/requests/{request}/status',          [LabController::class, 'updateRequestStatus'])->name('admin.lab.requests.status');

        // ── Xray ──────────────────────────────────────────────────────────────
        Route::get('xray/categories',                          [XrayController::class, 'categories'])->name('admin.xray.categories');
        Route::get('xray/categories/create',                   [XrayController::class, 'createCategory'])->name('admin.xray.categories.create');
        Route::post('xray/categories',                         [XrayController::class, 'storeCategory'])->name('admin.xray.categories.store');
        Route::get('xray/categories/{category}/edit',          [XrayController::class, 'editCategory'])->name('admin.xray.categories.edit');
        Route::patch('xray/categories/{category}',             [XrayController::class, 'updateCategory'])->name('admin.xray.categories.update');
        Route::delete('xray/categories/{category}',            [XrayController::class, 'destroyCategory'])->name('admin.xray.categories.destroy');
        Route::get('xray/tests',                               [XrayController::class, 'tests'])->name('admin.xray.tests');
        Route::get('xray/tests/create',                        [XrayController::class, 'createTest'])->name('admin.xray.tests.create');
        Route::post('xray/tests',                              [XrayController::class, 'storeTest'])->name('admin.xray.tests.store');
        Route::get('xray/tests/{test}/edit',                   [XrayController::class, 'editTest'])->name('admin.xray.tests.edit');
        Route::patch('xray/tests/{test}',                      [XrayController::class, 'updateTest'])->name('admin.xray.tests.update');
        Route::delete('xray/tests/{test}',                     [XrayController::class, 'destroyTest'])->name('admin.xray.tests.destroy');
        Route::get('xray/requests',                            [XrayController::class, 'requests'])->name('admin.xray.requests');
        Route::get('xray/requests/{request}',                  [XrayController::class, 'showRequest'])->name('admin.xray.requests.show');
        Route::patch('xray/requests/{request}/status',         [XrayController::class, 'updateRequestStatus'])->name('admin.xray.requests.status');

        // ── Doctors ───────────────────────────────────────────────────────────
        Route::resource('doctors', DoctorController::class, ['as' => 'admin']);
        Route::get('doctor-bookings',                          [DoctorController::class, 'bookings'])->name('admin.doctor-bookings.index');
        Route::patch('doctor-bookings/{booking}/status',       [DoctorController::class, 'updateBookingStatus'])->name('admin.doctor-bookings.status');

        // ── Nutrition ─────────────────────────────────────────────────────────
        Route::get('nutrition',                                [NutritionController::class, 'index'])->name('admin.nutrition.index');
        Route::get('nutrition/{nutrition}',                    [NutritionController::class, 'show'])->name('admin.nutrition.show');
        Route::patch('nutrition/{nutrition}/status',           [NutritionController::class, 'updateStatus'])->name('admin.nutrition.status');
        Route::delete('nutrition/{nutrition}',                 [NutritionController::class, 'destroy'])->name('admin.nutrition.destroy');

        // ── Patient Transfers ─────────────────────────────────────────────────
        Route::get('transfers',                                [PatientTransferController::class, 'index'])->name('admin.transfers.index');
        Route::get('transfers/{transfer}',                     [PatientTransferController::class, 'show'])->name('admin.transfers.show');
        Route::patch('transfers/{transfer}/status',            [PatientTransferController::class, 'updateStatus'])->name('admin.transfers.status');

        // ── Articles ──────────────────────────────────────────────────────────
        Route::resource('articles', ArticleController::class, ['as' => 'admin']);

        // ── Mothers Forum ─────────────────────────────────────────────────────
        Route::get('forum/categories',                         [ForumController::class, 'categories'])->name('admin.forum.categories');
        Route::get('forum/categories/create',                  [ForumController::class, 'createCategory'])->name('admin.forum.categories.create');
        Route::post('forum/categories',                        [ForumController::class, 'storeCategory'])->name('admin.forum.categories.store');
        Route::get('forum/categories/{category}/edit',         [ForumController::class, 'editCategory'])->name('admin.forum.categories.edit');
        Route::patch('forum/categories/{category}',            [ForumController::class, 'updateCategory'])->name('admin.forum.categories.update');
        Route::delete('forum/categories/{category}',           [ForumController::class, 'destroyCategory'])->name('admin.forum.categories.destroy');

        Route::get('forum/sub-categories',                     [ForumController::class, 'subCategories'])->name('admin.forum.sub-categories');
        Route::get('forum/sub-categories/create',              [ForumController::class, 'createSubCategory'])->name('admin.forum.sub-categories.create');
        Route::post('forum/sub-categories',                    [ForumController::class, 'storeSubCategory'])->name('admin.forum.sub-categories.store');
        Route::get('forum/sub-categories/{subCategory}/edit',  [ForumController::class, 'editSubCategory'])->name('admin.forum.sub-categories.edit');
        Route::patch('forum/sub-categories/{subCategory}',     [ForumController::class, 'updateSubCategory'])->name('admin.forum.sub-categories.update');
        Route::delete('forum/sub-categories/{subCategory}',    [ForumController::class, 'destroySubCategory'])->name('admin.forum.sub-categories.destroy');

        Route::get('forum/posts',                              [ForumController::class, 'posts'])->name('admin.forum.posts');
        Route::get('forum/posts/{post}',                       [ForumController::class, 'showPost'])->name('admin.forum.posts.show');
        Route::patch('forum/posts/{post}/toggle-status',       [ForumController::class, 'togglePostStatus'])->name('admin.forum.posts.toggle-status');
        Route::patch('forum/posts/{post}/toggle-pin',          [ForumController::class, 'togglePostPin'])->name('admin.forum.posts.toggle-pin');
        Route::delete('forum/posts/{post}',                    [ForumController::class, 'destroyPost'])->name('admin.forum.posts.destroy');

        Route::delete('forum/replies/{reply}',                 [ForumController::class, 'destroyReply'])->name('admin.forum.replies.destroy');
        Route::patch('forum/replies/{reply}/toggle-status',    [ForumController::class, 'toggleReplyStatus'])->name('admin.forum.replies.toggle-status');

        // ── Sihati — Report Templates ─────────────────────────────────────────
        Route::get('sihati/templates',                                        [ReportTemplateController::class, 'index'])->name('admin.sihati.templates.index');
        Route::get('sihati/templates/create',                                 [ReportTemplateController::class, 'create'])->name('admin.sihati.templates.create');
        Route::post('sihati/templates',                                       [ReportTemplateController::class, 'store'])->name('admin.sihati.templates.store');
        Route::get('sihati/templates/{template}',                             [ReportTemplateController::class, 'show'])->name('admin.sihati.templates.show');
        Route::get('sihati/templates/{template}/edit',                        [ReportTemplateController::class, 'edit'])->name('admin.sihati.templates.edit');
        Route::patch('sihati/templates/{template}',                           [ReportTemplateController::class, 'update'])->name('admin.sihati.templates.update');
        Route::delete('sihati/templates/{template}',                          [ReportTemplateController::class, 'destroy'])->name('admin.sihati.templates.destroy');
        Route::post('sihati/templates/{template}/fields',                     [ReportTemplateController::class, 'storeField'])->name('admin.sihati.templates.fields.store');
        Route::patch('sihati/fields/{field}',                                 [ReportTemplateController::class, 'updateField'])->name('admin.sihati.fields.update');
        Route::delete('sihati/fields/{field}',                                [ReportTemplateController::class, 'destroyField'])->name('admin.sihati.fields.destroy');

        // ── Sihati — Document Templates ───────────────────────────────────────
        Route::get('sihati/documents/authorization',                          [DocumentTemplateController::class, 'editAuthorization'])->name('admin.sihati.documents.authorization');
        Route::patch('sihati/documents/authorization',                        [DocumentTemplateController::class, 'updateAuthorization'])->name('admin.sihati.documents.authorization.update');
        Route::get('sihati/documents/pledge',                                 [DocumentTemplateController::class, 'editPledge'])->name('admin.sihati.documents.pledge');
        Route::patch('sihati/documents/pledge',                               [DocumentTemplateController::class, 'updatePledge'])->name('admin.sihati.documents.pledge.update');

        // ── Sihati — Rooms ────────────────────────────────────────────────────
        Route::get('sihati/rooms',                                            [RoomController::class, 'index'])->name('admin.sihati.rooms.index');
        Route::get('sihati/rooms/{room}',                                     [RoomController::class, 'show'])->name('admin.sihati.rooms.show');
        Route::patch('sihati/rooms/{room}/toggle',                            [RoomController::class, 'toggleActive'])->name('admin.sihati.rooms.toggle');
        Route::post('sihati/rooms/{room}/members',                            [RoomController::class, 'addMember'])->name('admin.sihati.rooms.members.add');
        Route::delete('sihati/rooms/{room}/members/{member}',                 [RoomController::class, 'removeMember'])->name('admin.sihati.rooms.members.remove');
        Route::post('sihati/rooms/{room}/assign-template',                    [RoomController::class, 'assignTemplate'])->name('admin.sihati.rooms.assign-template');

        // ── Sihati — Outside Medications ─────────────────────────────────────
        Route::get('sihati/medications',                                      [MedicationController::class, 'index'])->name('admin.sihati.medications.index');
        Route::get('sihati/medications/{medication}',                         [MedicationController::class, 'show'])->name('admin.sihati.medications.show');

    });
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login',  [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
