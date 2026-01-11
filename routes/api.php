<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OwnerBookingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/showprofile', [ProfileController::class, 'show']);
    Route::post('/update', [ProfileController::class, 'update']);
    Route::post('/update-image', [ProfileController::class, 'updateImage']);
});
/****************/
Route::middleware('auth:sanctum')->post(
    '/apartments',
    [ApartmentController::class, 'store']
);
Route::middleware('auth:sanctum')->get('/apartment_user', [ApartmentController::class, 'myApartments']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::post('/bookings/{id}/rate', [BookingController::class, 'rate']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
});
Route::get('/apartments/{id}/booked-dates', [BookingController::class, 'ShowbookedDates']);
Route::get('apartments/{id}/rating', [BookingController::class, 'getApartmentStats']);

// owner
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/owner/bookings/{id}/approve', [OwnerBookingController::class, 'approve']);
    Route::post('/owner/bookings/{id}/reject', [OwnerBookingController::class, 'reject']);
    Route::get('/owner/bookings', [OwnerBookingController::class, 'index']);
});

// notifications
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/notifications', [NotificationsController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationsController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationsController::class, 'markAllAsRead']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/{apartment}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
});
