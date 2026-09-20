<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\BranchApiController;
use Modules\Company\Http\Controllers\CompanyApiController;
use Modules\Company\Http\Controllers\DepartmentApiController;
use Modules\Company\Http\Controllers\FiscalYearApiController;

Route::middleware('auth:api')->prefix('company')->name('company.')->group(function () {
    Route::apiResource('companies', CompanyApiController::class);
    Route::apiResource('branches', BranchApiController::class);
    Route::apiResource('departments', DepartmentApiController::class);
    Route::apiResource('fiscal-years', FiscalYearApiController::class);
});
