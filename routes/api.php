<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[UserController::class,'logout']);
});
/****************/
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
Route::middleware('auth:sanctum')->post(
    '/apartments',
    [ApartmentController::class, 'store']
);
Route::middleware('auth:sanctum')->get('/apartment_user', [ApartmentController::class, 'myApartments']);

