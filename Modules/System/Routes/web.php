<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\CountryController;
use Modules\System\Http\Controllers\CurrencyController;
use Modules\System\Http\Controllers\ExchangeRateController;
use Modules\System\Http\Controllers\LanguageController;
use Modules\System\Http\Controllers\TimeZoneController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('system')->group(function() {
    Route::get('/', 'SystemController@index');
    Route::resource('country', CountryController::class);
    Route::resource('currency', CurrencyController::class);
    Route::resource('exchange-rate', ExchangeRateController::class);
    Route::resource('language', LanguageController::class);
    Route::resource('time-zone', TimeZoneController::class);
});
