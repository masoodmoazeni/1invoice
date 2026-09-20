<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\BranchController;
use Modules\Company\Http\Controllers\CompanyController;
use Modules\Company\Http\Controllers\DepartmentController;
use Modules\Company\Http\Controllers\FiscalYearController;

Route::prefix('company')->name('company.')->group(function () {
    Route::resource('branch', BranchController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('fiscal-year', FiscalYearController::class);
});
