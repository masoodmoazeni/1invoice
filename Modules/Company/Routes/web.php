<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\BranchController;
use Modules\Company\Http\Controllers\CompanyController;
use Modules\Company\Http\Controllers\DepartmentController;
use Modules\Company\Http\Controllers\FiscalYearController;
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

Route::prefix('company')->group(function() {
//    Route::get('/', 'SystemController@index');
    Route::resource('branch', BranchController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('fiscal-year', FiscalYearController::class);
});
