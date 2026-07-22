<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\v1\CategoryController;
use Modules\Setting\Http\Controllers\v1\CityController;
use Modules\Setting\Http\Controllers\v1\CommentController;
use Modules\Setting\Http\Controllers\v1\CountryController;
use Modules\Setting\Http\Controllers\v1\PageBuilderController;
use Modules\Setting\Http\Controllers\v1\SaleAdvantageController;
use Modules\Setting\Http\Controllers\v1\StateController;
use Modules\Setting\Http\Controllers\v1\BlogController;
use Modules\Setting\Http\Controllers\v1\FaqController;
use Modules\Setting\Http\Controllers\v1\HeaderMenuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/country', [CountryController::class, 'index']);
        Route::get('/state', [StateController::class, 'index']);
        Route::get('/city', [CityController::class, 'index']);
        Route::get('/zone/all', [CountryController::class, 'all']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/list', [CategoryController::class, 'index']);
        Route::get('/all', [CategoryController::class, 'all']);
        Route::get('/sub/list', [CategoryController::class, 'subcategory']);
    });

    Route::prefix('types')->group(function () {
        Route::get('/sale', [SaleAdvantageController::class, 'sale']);
        Route::get('/occupancy', [SaleAdvantageController::class, 'occupancy']);
        Route::get('/business', [SaleAdvantageController::class, 'business']);
        Route::get('/all', [SaleAdvantageController::class, 'all']);
    });

    Route::prefix('comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
    });

    Route::prefix('emails')->group(function () {
        Route::post('/', [CommentController::class, 'emails']);
    });

    Route::prefix('pages')->group(function () {
        Route::get('/slugs', [PageBuilderController::class, 'slugs']);
        Route::get('/', [PageBuilderController::class, 'index']);
        Route::get('/detail/{slug}', [PageBuilderController::class, 'showSlug']);
    });

    Route::prefix('blogs')->group(function () {
        Route::get('/slugs', [BlogController::class, 'slugs']);
        Route::get('/', [BlogController::class, 'index']);
        Route::get('/detail/{slug}', [BlogController::class, 'showSlug']);
    });

    Route::get('/faq', [FaqController::class, 'index']);
    Route::get('/header-menu', [HeaderMenuController::class, 'index']);
});
