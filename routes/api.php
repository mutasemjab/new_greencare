<?php

use App\Http\Controllers\Api\v1\ArticleController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\AddressController;
use App\Http\Controllers\Api\v1\BannerController;
use App\Http\Controllers\Api\v1\BathingController;
use App\Http\Controllers\Api\v1\CareerController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\v1\CareController;
use App\Http\Controllers\Api\v1\DeliveryZoneController;
use App\Http\Controllers\Api\v1\DoctorController;
use App\Http\Controllers\Api\v1\ForumController;
use App\Http\Controllers\Api\v1\LabController;
use App\Http\Controllers\Api\v1\MedicationController;
use App\Http\Controllers\Api\v1\NotificationController;
use App\Http\Controllers\Api\v1\NutritionController;
use App\Http\Controllers\Api\v1\NursingController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\SihatiController;
use App\Http\Controllers\Api\v1\StoreCategoryController;
use App\Http\Controllers\Api\v1\TransferController;
use App\Http\Controllers\Api\v1\VisitFormController;
use App\Http\Controllers\Api\v1\XrayController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Auth (public) ─────────────────────────────────────────────────────
    Route::post('auth/send-otp',   [AuthController::class, 'sendOtp']);
    Route::post('auth/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

    // ── Public content ────────────────────────────────────────────────────
    Route::get('banners',                        [BannerController::class, 'index']);
    Route::get('store/categories',               [StoreCategoryController::class, 'index']);
    Route::get('store/categories/{id}/products', [StoreCategoryController::class, 'products']);
    Route::get('store/products',                 [ProductController::class, 'index']);
    Route::get('store/products/{id}',            [ProductController::class, 'show']);
    Route::get('nursing/types',                  [NursingController::class, 'types']);
    Route::get('care/services',                  [CareController::class, 'services']);
    Route::get('lab/categories',                 [LabController::class, 'categories']);
    Route::get('lab/tests',                      [LabController::class, 'tests']);
    Route::get('xray/categories',                [XrayController::class, 'categories']);
    Route::get('xray/tests',                     [XrayController::class, 'tests']);
    Route::get('doctors',                        [DoctorController::class, 'index']);
    Route::get('doctors/{id}',                   [DoctorController::class, 'show']);
    Route::get('articles',                       [ArticleController::class, 'index']);
    Route::get('articles/{id}',                  [ArticleController::class, 'show']);
    Route::get('forum/categories',               [ForumController::class, 'categories']);
    Route::get('forum/sub-categories',           [ForumController::class, 'subCategories']);
    Route::get('forum/posts',                    [ForumController::class, 'posts']);
    Route::get('forum/posts/{id}',               [ForumController::class, 'showPost']);
    Route::get('sihati/documents/{type}',        [SihatiController::class, 'getDocument']);
    Route::get('delivery-zones',                 [DeliveryZoneController::class, 'index']);

    // ── Protected routes ──────────────────────────────────────────────────
    Route::middleware('auth:user-api')->group(function () {

        // Auth
        Route::post('auth/logout',   [AuthController::class, 'logout']);
        Route::get('auth/me',        [AuthController::class, 'me']);
        Route::put('auth/profile',   [AuthController::class, 'updateProfile']);
        Route::put('auth/fcm-token', [AuthController::class, 'updateFcmToken']);

        // Firebase Auth bridge
        Route::post('firebase/token', [AuthController::class, 'firebaseToken']);

        // Addresses
        Route::get('addresses',                [AddressController::class, 'index']);
        Route::post('addresses',               [AddressController::class, 'store']);
        Route::put('addresses/{id}',           [AddressController::class, 'update']);
        Route::delete('addresses/{id}',        [AddressController::class, 'destroy']);
        Route::patch('addresses/{id}/default', [AddressController::class, 'setDefault']);

        // Cart
        Route::get('cart',               [CartController::class, 'show']);
        Route::post('cart/items',        [CartController::class, 'addItem']);
        Route::patch('cart/items/{id}',  [CartController::class, 'updateItem']);
        Route::delete('cart/items/{id}', [CartController::class, 'removeItem']);
        Route::delete('cart',            [CartController::class, 'clear']);

        // Orders
        Route::post('orders',     [OrderController::class, 'checkout']);
        Route::get('orders',      [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);

        // Nursing
        Route::post('nursing/requests',     [NursingController::class, 'store']);
        Route::get('nursing/requests',      [NursingController::class, 'index']);
        Route::get('nursing/requests/{id}', [NursingController::class, 'show']);

        // Bathing
        Route::post('bathing/redeem',   [BathingController::class, 'redeem']);
        Route::post('bathing/requests', [BathingController::class, 'store']);
        Route::get('bathing/requests',  [BathingController::class, 'index']);

        // Care
        Route::post('care/requests',     [CareController::class, 'store']);
        Route::get('care/requests',      [CareController::class, 'index']);
        Route::get('care/requests/{id}', [CareController::class, 'show']);

        // Lab
        Route::post('lab/requests',     [LabController::class, 'store']);
        Route::get('lab/requests',      [LabController::class, 'index']);
        Route::get('lab/results',       [LabController::class, 'results']);
        Route::get('lab/requests/{id}', [LabController::class, 'show']);

        // Xray
        Route::post('xray/requests',     [XrayController::class, 'store']);
        Route::get('xray/requests',      [XrayController::class, 'index']);
        Route::get('xray/requests/{id}', [XrayController::class, 'show']);

        // Doctors & bookings
        Route::post('doctors/{id}/book', [DoctorController::class, 'book']);
        Route::get('bookings',           [DoctorController::class, 'bookings']);
        Route::get('bookings/{id}',      [DoctorController::class, 'bookingShow']);

        // Nutrition
        Route::post('nutrition/requests',      [NutritionController::class, 'store']);
        Route::get('nutrition/requests',       [NutritionController::class, 'index']);
        Route::get('nutrition/requests/{id}',  [NutritionController::class, 'show']);

        // Patient Transfer
        Route::post('transfers',     [TransferController::class, 'store']);
        Route::get('transfers',      [TransferController::class, 'index']);
        Route::get('transfers/note', [TransferController::class, 'display_note_in_transfer']);
        Route::get('transfers/{id}', [TransferController::class, 'show']);

        // Forum
        Route::post('forum/posts',              [ForumController::class, 'storePost']);
        Route::delete('forum/posts/{id}',       [ForumController::class, 'destroyPost']);
        Route::post('forum/posts/{id}/replies', [ForumController::class, 'storeReply']);
        Route::delete('forum/replies/{id}',     [ForumController::class, 'destroyReply']);

        // Sihati (room system) — legacy single-room endpoint, kept for now
        Route::get('sihati/my-room',                                   [SihatiController::class, 'myRoom']);

        // Sihati — items feed (replaces sihati/my-room), intake, lookups
        Route::get('sihati/items',                                     [SihatiController::class, 'items']);
        Route::post('sihati/rooms',                                    [SihatiController::class, 'storeRoom']);
        Route::get('sihati/users/search',                              [SihatiController::class, 'searchUsers']);
        Route::get('sihati/diagnoses',                                 [SihatiController::class, 'diagnoses']);
        Route::get('sihati/chronic-diseases',                          [SihatiController::class, 'chronicDiseases']);

        Route::get('sihati/rooms/{id}',                                [SihatiController::class, 'roomDetail']);
        Route::get('sihati/rooms/{id}/reports',                        [SihatiController::class, 'roomReports']);
        Route::post('sihati/rooms/{id}/reports',                       [SihatiController::class, 'submitReport']);
        Route::get('sihati/rooms/{id}/reports/{reportId}',             [SihatiController::class, 'reportDetail']);
        Route::get('sihati/rooms/{id}/doctor-notes',                   [SihatiController::class, 'doctorNotesIndex']);
        Route::post('sihati/rooms/{id}/doctor-notes',                  [SihatiController::class, 'storeDoctorNote']);
        Route::get('sihati/rooms/{id}/doctor-orders',                  [SihatiController::class, 'doctorOrders']);
        Route::post('sihati/rooms/{id}/doctor-orders',                 [SihatiController::class, 'storeDoctorOrder']);
        Route::post('sihati/rooms/{id}/doctor-orders/{orderId}/reply', [SihatiController::class, 'replyOrder']);
        Route::get('sihati/rooms/{id}/medications',                    [SihatiController::class, 'roomMedications']);
        Route::post('sihati/rooms/{id}/medications',                   [SihatiController::class, 'addMedication']);
        Route::post('sihati/rooms/{id}/complaints',                    [SihatiController::class, 'storeComplaint']);
        Route::post('sihati/rooms/{id}/chat-image',                    [SihatiController::class, 'uploadChatImage']);

        // Sihati — medical visit forms (parallel, chat-less item type)
        Route::get('sihati/visit-form-schema',  [VisitFormController::class, 'schema']);
        Route::post('sihati/visit-forms',       [VisitFormController::class, 'store']);
        Route::get('sihati/visit-forms/{id}',   [VisitFormController::class, 'show']);

        // Notifications
        Route::get('notifications',                [NotificationController::class, 'index']);
        Route::get('notifications/unread-count',    [NotificationController::class, 'unreadCount']);
        Route::patch('notifications/read-all',      [NotificationController::class, 'markAllRead']);
        Route::patch('notifications/{id}/read',     [NotificationController::class, 'markRead']);

        // Medications (outside rooms — patient only)
        Route::get('medications',         [MedicationController::class, 'index']);
        Route::post('medications',        [MedicationController::class, 'store']);
        Route::delete('medications/{id}', [MedicationController::class, 'destroy']);

        // Careers
        Route::post('careers/apply', [CareerController::class, 'apply']);
    });
});
