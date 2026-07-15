<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RefreshTokenController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\StaffAvailabilityController;
use App\Http\Controllers\StaffLeaveController;


// app/
// ├── Http/
// │   ├── Controllers/
// │   │   └── ServiceController.php
// │   └── Requests/
// │       ├── Service/StoreServiceRequest.php
// │       └── Service/UpdateServiceRequest.php
// ├── Repositories/Service/
// │   └── ServiceRepository.php
//         ServiceRepositoryInterface.php
// └── Models/
//     └── Service.php

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/refresh-token', [RefreshTokenController::class, 'refresh']);

// Home page
Route::get('/category-level/{level}', [CategoryController::class, 'getCategoriesByLevelForClient']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Route::get('/staff', [StaffController::class, 'index']);
// Route::get('/staff/{staff}', [StaffController::class, 'show']);

// Route::get('/gallery', [GalleryController::class, 'index']);
// Route::get('/faqs', [FaqController::class, 'index']);

Route::get('/reviews', [ReviewController::class, 'index']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);


    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */

    // Route::middleware('user.middleware')->group(function () {

    //     Route::apiResource('appointments', AppointmentController::class);

    //     Route::post(
    //         'appointments/{appointment}/cancel',
    //         [AppointmentController::class, 'cancel']
    //     );

    //     Route::post(
    //         'appointments/{appointment}/review',
    //         [ReviewController::class, 'store']
    //     );
    // });

    /*
    |--------------------------------------------------------------------------
    | Staff Routes
    |--------------------------------------------------------------------------
    */

    // Route::middleware('staff.middleware')->group(function () {

    //     Route::get('/staff/dashboard', [StaffController::class, 'dashboard']);

    //     Route::get('/staff/appointments', [AppointmentController::class, 'staffAppointments']);

    //     Route::patch(
    //         '/staff/appointments/{appointment}/status',
    //         [AppointmentController::class, 'updateStatus']
    //     );

    //     Route::apiResource(
    //         'staff-availability',
    //         StaffAvailabilityController::class
    //     );

    //     Route::apiResource(
    //         'staff-leaves',
    //         StaffLeaveController::class
    //     );
    // });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    // Route::middleware('admin.middleware')->group(function () {

    //     Route::get('/admin/dashboard', [UserController::class, 'dashboard']);

        Route::apiResource('categories', CategoryController::class);

    Route::apiResource('services', ServiceController::class);

    //     Route::apiResource('staff', StaffController::class);

    //     Route::apiResource('users', UserController::class);

    //     Route::apiResource('gallery', GalleryController::class);

    //     Route::apiResource('faqs', FaqController::class);

    //     Route::apiResource('reviews', ReviewController::class);

    //     Route::get(
    //         '/appointments/all',
    //         [AppointmentController::class, 'index']
    //     );

    //     Route::patch(
    //         '/appointments/{appointment}/status',
    //         [AppointmentController::class, 'updateStatus']
    //     );
    // });
});
