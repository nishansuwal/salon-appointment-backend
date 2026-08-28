<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\ProfileController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TestimonialController;
// use App\Http\Controllers\AddressController;
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

// variables should be camelCase
//Database columns should be in snake_case
//Route should be in kebab-case

// php artisan migrate:fresh --seed

//
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/refresh-token', [RefreshTokenController::class, 'refresh']);

// Home page
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category-level/{level}', [CategoryController::class, 'getCategoriesByLevelForClient']);

Route::get('/client-services', [ServiceController::class, 'clientServices']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

Route::apiResource('staffs', StaffProfileController::class)->only('index', 'show');

Route::get('/gallery', [GalleryController::class, 'index']);
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/settings', [SettingController::class, 'show']);

Route::get('/services/{service}/reviews', [ReviewController::class, 'serviceReviews']);

Route::post(
    'available-staff',
    [StaffProfileController::class, 'availableStaff']
);


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('user.middleware')->group(function () {


        Route::post(
            'appointments/{appointment}/cancel',
            [AppointmentController::class, 'cancel']
        );

        Route::get(
            'reviews/my-reviews',
            [ReviewController::class, 'myReviews']
        );
        Route::get(
            'services/{serviceId}/review-status',
            [ReviewController::class, 'reviewStatus']
        );

        Route::apiResource('reviews', ReviewController::class)->except('index', 'show');


        Route::get(
            '/user/appointments',
            [AppointmentController::class, 'getUserAppointments']
        );
        Route::post(
            '/user/appointments',
            [AppointmentController::class, 'store']
        );

        Route::get(
            '/user/appointments/{appointment}',
            [AppointmentController::class, 'userShow']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Staff Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('staff.middleware')->group(function () {

        // Route::get('/staff/dashboard', [StaffProfileController::class, 'dashboard']);

        Route::get('/staff/appointments', [AppointmentController::class, 'staffAppointments']);

        Route::patch(
            '/staff/appointments/{appointment}/status',
            [AppointmentController::class, 'updateStatus']
        );

        Route::get(
            '/staff/appointments/{appointment}',
            [AppointmentController::class, 'staffShow']
        );

        Route::apiResource(
            'staff/availability',
            StaffAvailabilityController::class
        );

        Route::get(
            'staff/leaves',
            [StaffLeaveController::class, 'staffIndex']
        );

        Route::post(
            'staff/leaves',
            [StaffLeaveController::class, 'store']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.middleware')->group(function () {

        // Route::get('/admin/dashboard', [UserController::class, 'dashboard']);

        Route::apiResource('categories', CategoryController::class)->except('index');

        Route::apiResource('services', ServiceController::class)->except('show');

        Route::apiResource('staffs', StaffProfileController::class)->except('index', 'show');;

        Route::apiResource('users', UserController::class);

        Route::apiResource('gallery', GalleryController::class)->except('index');

        Route::apiResource('testimonials', TestimonialController::class)->except('index');

        Route::apiResource('faqs', FaqController::class)->except('index');

        Route::put('/settings', [SettingController::class, 'update']);

        Route::put('/reviews/updateStatus/{review}', [ReviewController::class, 'updateStatus']);
        Route::get('/reviews', [ReviewController::class, 'index']);


        Route::get(
            '/appointments/all',
            [AppointmentController::class, 'index']
        );

        Route::patch(
            '/appointments/{appointment}/status',
            [AppointmentController::class, 'updateStatus']
        );

        Route::get(
            '/appointments/{appointment}',
            [AppointmentController::class, 'show']
        );

        Route::get(
            'availability/staff',
            [StaffAvailabilityController::class, 'adminIndex']
        );

        Route::get(
            'leaves',
            [StaffLeaveController::class, 'adminIndex']
        );

        Route::patch(
            'leaves/{id}/approve',
            [StaffLeaveController::class, 'approve']
        );

        Route::patch(
            'leaves/{id}/reject',
            [StaffLeaveController::class, 'reject']
        );
    });
});
