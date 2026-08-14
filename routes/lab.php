<?php

use App\Http\Controllers\Lab\LoginController;
use App\Http\Controllers\Lab\RequestController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group(['prefix' => 'lab', 'middleware' => 'auth:lab'], function () {

        Route::get('/', [RequestController::class, 'index'])->name('lab.dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('lab.logout');

        Route::get('requests', [RequestController::class, 'index'])->name('lab.requests');
        Route::get('requests/{labRequest}', [RequestController::class, 'show'])->name('lab.requests.show');
        Route::patch('requests/{labRequest}/status', [RequestController::class, 'updateStatus'])->name('lab.requests.status');
        Route::post('requests/{labRequest}/result', [RequestController::class, 'uploadResult'])->name('lab.requests.result');

    });
});

Route::group(['namespace' => 'Lab', 'prefix' => 'lab', 'middleware' => 'guest:lab'], function () {
    Route::get('login', [LoginController::class, 'show_login_view'])->name('lab.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('lab.login');
});
