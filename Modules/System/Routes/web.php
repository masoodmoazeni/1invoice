<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\CountryController;
use Modules\System\Http\Controllers\CurrencyController;
use Modules\System\Http\Controllers\ExchangeRateController;
use Modules\System\Http\Controllers\LanguageController;
use Modules\System\Http\Controllers\SystemController;
use Modules\System\Http\Controllers\TimeZoneController;

Route::prefix('system')->name('system.')->group(function () {
    Route::get('/', [SystemController::class, 'index'])->name('index');
    Route::resource('country', CountryController::class);
    Route::resource('currency', CurrencyController::class);
    Route::resource('exchange-rate', ExchangeRateController::class);
    Route::resource('language', LanguageController::class);
    Route::resource('time-zone', TimeZoneController::class);
});
