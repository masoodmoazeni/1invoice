<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\SendgridWebhookController;
use App\Http\Controllers\StripeCheckoutListingCoverController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminUserController;
use Modules\Admin\Http\Controllers\AdminSettingsController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

use Illuminate\Support\Facades\Log;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
     ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('/stripe/checkout-cover/{listId}/checkout.jpg', [StripeCheckoutListingCoverController::class, 'show'])
    ->middleware('signed')
    ->whereNumber('listId')
    ->name('stripe.checkout-listing-cover');

Route::post('/sendgrid/webhook', [SendgridWebhookController::class, 'handle'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
// Route::get('/stripe/payment/details', function (Request $request) {

//     \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

//     if (!$request->has('session_id')) {
//         return response()->json(['error' => 'session_id is required'], 400);
//     }

//     try {
//         $session = \Stripe\Checkout\Session::retrieve(
//             $request->session_id,
//             ['expand' => ['payment_intent']]
//         );

//         return response()->json([
//             'session'        => $session,
//             'payment_intent' => $session->payment_intent ?? null
//         ]);

//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// });
