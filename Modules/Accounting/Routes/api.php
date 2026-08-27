<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Accounting\Http\Controllers\v1\CompanyController;
use Modules\Accounting\Http\Controllers\v1\CountryController;
use Modules\Accounting\Http\Controllers\v1\LanguageController;
use Modules\Accounting\Http\Controllers\v1\AccountController;
use Modules\Accounting\Http\Controllers\v1\BankAccountController;
use Modules\Accounting\Http\Controllers\v1\AccountTemplateController;
use Modules\Accounting\Http\Controllers\v1\AccountTemplateLineController;
use Modules\Accounting\Http\Controllers\v1\BranchController;
use Modules\Accounting\Http\Controllers\v1\CurrencyController;
use Modules\Accounting\Http\Controllers\v1\DepartmentController;
use Modules\Accounting\Http\Controllers\v1\DimensionController;
use Modules\Accounting\Http\Controllers\v1\DocumentTypeController;
use Modules\Accounting\Http\Controllers\v1\TimezoneController;
use Modules\Accounting\Http\Controllers\v1\JournalController;
use Modules\Accounting\Http\Controllers\v1\JournalEntryController;
use Modules\Accounting\Http\Controllers\v1\DimensionValueController;

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

    Route::prefix('account-template-line')->group(function () {
        Route::get('/', [AccountTemplateLineController::class, 'index']);
        Route::post('/', [AccountTemplateLineController::class, 'create']);
        Route::get('/{id}', [AccountTemplateLineController::class, 'show']);
        Route::put('/{id}', [AccountTemplateLineController::class, 'update']);
        Route::delete('/{id}', [AccountTemplateLineController::class, 'destroy']);
    });

    Route::prefix('bank-account')->group(function () {
        Route::get('/', [BankAccountController::class, 'index']);
        Route::post('/', [BankAccountController::class, 'create']);
        Route::get('/{id}', [BankAccountController::class, 'show']);
        Route::put('/{id}', [BankAccountController::class, 'update']);
        Route::delete('/{id}', [BankAccountController::class, 'destroy']);
    });

    Route::prefix('branch')->group(function () {
        Route::get('/', [BranchController::class, 'index']);
        Route::post('/', [BranchController::class, 'create']);
        Route::get('/{id}', [BranchController::class, 'show']);
        Route::put('/{id}', [BranchController::class, 'update']);
        Route::delete('/{id}', [BranchController::class, 'destroy']);
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

    Route::prefix('currency')->group(function () {
        Route::get('/', [CurrencyController::class, 'index']);
        Route::post('/', [CurrencyController::class, 'create']);
        Route::get('/{id}', [CurrencyController::class, 'show']);
        Route::put('/{id}', [CurrencyController::class, 'update']);
        Route::delete('/{id}', [CurrencyController::class, 'destroy']);
    });

    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'create']);
        Route::get('/{id}', [DepartmentController::class, 'show']);
        Route::put('/{id}', [DepartmentController::class, 'update']);
        Route::delete('/{id}', [DepartmentController::class, 'destroy']);
    });

    Route::prefix('dimension')->group(function () {
        Route::get('/', [DimensionController::class, 'index']);
        Route::post('/', [DimensionController::class, 'create']);
        Route::get('/{id}', [DimensionController::class, 'show']);
        Route::put('/{id}', [DimensionController::class, 'update']);
        Route::delete('/{id}', [DimensionController::class, 'destroy']);
    });

    Route::prefix('dimension-value')->group(function () {
        Route::get('/', [DimensionValueController::class, 'index']);
        Route::post('/', [DimensionValueController::class, 'create']);
        Route::get('/{id}', [DimensionValueController::class, 'show']);
        Route::put('/{id}', [DimensionValueController::class, 'update']);
        Route::delete('/{id}', [DimensionValueController::class, 'destroy']);
    });

    Route::prefix('document-types')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index']);
        Route::post('/', [DocumentTypeController::class, 'create']);
        Route::get('/{id}', [DocumentTypeController::class, 'show']);
        Route::put('/{id}', [DocumentTypeController::class, 'update']);
        Route::delete('/{id}', [DocumentTypeController::class, 'destroy']);
    });

    Route::prefix('journal')->group(function () {
        Route::get('/', [JournalController::class, 'index']);
        Route::post('/', [JournalController::class, 'create']);
        Route::get('/{id}', [JournalController::class, 'show']);
        Route::put('/{id}', [JournalController::class, 'update']);
        Route::delete('/{id}', [JournalController::class, 'destroy']);
    });

    Route::prefix('journal-entry')->group(function () {
        Route::get('/', [JournalEntryController::class, 'index']);
        Route::post('/', [JournalEntryController::class, 'create']);
        Route::get('/{id}', [JournalEntryController::class, 'show']);
        Route::put('/{id}', [JournalEntryController::class, 'update']);
        Route::delete('/{id}', [JournalEntryController::class, 'destroy']);
    });

    Route::prefix('language')->group(function () {
        Route::get('/', [LanguageController::class, 'index']);
        Route::post('/', [LanguageController::class, 'create']);
        Route::get('/{id}', [LanguageController::class, 'show']);
        Route::put('/{id}', [LanguageController::class, 'update']);
        Route::delete('/{id}', [LanguageController::class, 'destroy']);
    });


    Route::prefix('timezone')->group(function () {
        Route::get('/', [TimezoneController::class, 'index']);
        Route::post('/', [TimezoneController::class, 'create']);
        Route::get('/{id}', [TimezoneController::class, 'show']);
        Route::put('/{id}', [TimezoneController::class, 'update']);
        Route::delete('/{id}', [TimezoneController::class, 'destroy']);
    });

});
