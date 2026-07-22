<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\v1\GoogleController;
use Modules\User\Http\Controllers\v1\ProfileController;
use Modules\User\Http\Controllers\v1\UserController;
use Modules\User\Http\Controllers\v1\UserProfileController;

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
    Route::prefix('users')->group(function () {
        Route::post('/login/google/token', [GoogleController::class, 'handleGoogleCallback']);

        Route::prefix('team')->group(function () {
            Route::get('/slugs', [UserProfileController::class, 'slugs']);
            Route::get('/', [UserProfileController::class, 'index']);
            Route::get('/{slug}', [UserProfileController::class, 'show']);
        });
        
        Route::post('/signup', [UserController::class, 'signup'])
            ->middleware('throttle:public-forms');
        Route::post('/signup/test', [UserController::class, 'signupTest']);
        Route::post('/signin', [UserController::class, 'signin']);
        Route::post('/verifiedemail', [UserController::class, 'verifiedEmail']);
        Route::post('/accept-invitation', [UserController::class, 'acceptInvitation']);
        Route::post('/forgetpassword', [UserController::class, 'forgetpassword']);
        Route::post('/forgetpassword/verified', [UserController::class, 'ForgetPasswordVerified']);
            Route::group(['middleware' => 'auth:api'], function () {
                Route::prefix('profile')->group(function () {
                    Route::get('/data', [ProfileController::class, 'index']);
                    Route::put('/', [ProfileController::class, 'update']);
                    Route::post('/changepassword', [ProfileController::class, 'changepassword']);
                    Route::get('/email', [ProfileController::class, 'email']);
                    Route::post('/image', [ProfileController::class, 'image']);
                    Route::post('/logout', [ProfileController::class, 'logout']);
                });
            });
        });

        
});

