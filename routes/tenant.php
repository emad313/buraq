<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/
Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ])->prefix('api')->group(function () {

    // Register new user in tenant
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    // Login user in tenant
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

    // User email authentication Verification Routes
    // Verify email API. it will send with email
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\VerifyEmailController::class, '__invoke'])
    ->middleware(['signed','throttle:6,1'])
    ->name('verification.verify');

    // Resend link to verify email
    Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
    })->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

    // Authorized routes
    Route::middleware('auth:api')->group(function(){
        // Single User data in tenant
        Route::get('/user', [App\Http\Controllers\AuthController::class, 'userData']);
    });

    // Route::get('/', function () {
    //     return [
    //         'tenant' => tenant(),
    //         'user' => User::get(),
    //     ];
    // });
 });
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('app');
    });
    // Route::get('/', function () {

    //     return [
    //         'tenant' => tenant(),
    //         'user' => User::get(),
    //     ];
    // });
});
