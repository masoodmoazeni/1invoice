<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Accounting\Http\Controllers\v1\CompanyController;
use Modules\Accounting\Http\Controllers\v1\CountryController;
use Modules\Accounting\Http\Controllers\v1\LanguageController;
use Modules\Accounting\Http\Controllers\v1\AccountController;
use Modules\Accounting\Http\Controllers\v1\BankAccountController;
use Modules\Accounting\Http\Controllers\v1\AccountTemplateController;
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
    
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::post('/', [AccountController::class, 'create']);
        Route::get('/{id}', [AccountController::class, 'show']);
        Route::put('/{id}', [AccountController::class, 'update']);
        Route::delete('/{id}', [AccountController::class, 'destroy']);
    });

    Route::prefix('account-template')->group(function () {
        Route::get('/', [AccountTemplateController::class, 'index']);
        Route::post('/', [AccountTemplateController::class, 'create']);
        Route::get('/{id}', [AccountTemplateController::class, 'show']);
        Route::put('/{id}', [AccountTemplateController::class, 'update']);
        Route::delete('/{id}', [AccountTemplateController::class, 'destroy']);
    });

    Route::prefix('bank-account')->group(function () {
        Route::get('/', [BankAccountController::class, 'index']);
        Route::post('/', [BankAccountController::class, 'create']);
        Route::get('/{id}', [BankAccountController::class, 'show']);
        Route::put('/{id}', [BankAccountController::class, 'update']);
        Route::delete('/{id}', [BankAccountController::class, 'destroy']);
    });
    
    Route::prefix('company')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'create']);
        Route::get('/{id}', [CompanyController::class, 'show']);
        Route::put('/{id}', [CompanyController::class, 'update']);
        Route::delete('/{id}', [CompanyController::class, 'destroy']);
    });

    Route::prefix('country')->group(function () {
        Route::get('/', [CountryController::class, 'index']);
        Route::post('/', [CountryController::class, 'create']);
        Route::get('/{id}', [CountryController::class, 'show']);
        Route::put('/{id}', [CountryController::class, 'update']);
        Route::delete('/{id}', [CountryController::class, 'destroy']);
    });

    Route::prefix('language')->group(function () {
        Route::get('/', [LanguageController::class, 'index']);
        Route::post('/', [LanguageController::class, 'create']);
        Route::get('/{id}', [LanguageController::class, 'show']);
        Route::put('/{id}', [LanguageController::class, 'update']);
        Route::delete('/{id}', [LanguageController::class, 'destroy']);
    });

});
