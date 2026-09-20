<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\CompanyApiController;

Route::middleware('auth:api')->prefix('company')->name('company.')->group(function () {
    Route::apiResource('companies', CompanyApiController::class);
});
