<?php
use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login',[AdminController::class,'login'])->name('admin.login');
Route::post('/admin/login',[AdminController::class,'loginSubmit'])->name('admin.login.submit');
Route::post('/admin/logout',[AdminController::class,'logout'])->name('admin.logout');

Route::middleware(['auth','admin'])->prefix('admin')->group(function(){
    Route::get('/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
    Route::get('/pending',[AdminController::class,'pending'])->name('admin.pending');
    Route::get('/users',[AdminController::class,'users'])->name('admin.users');
    Route::get('/reports',[AdminController::class,'reports'])->name('admin.reports');
    Route::get('/approve/{id}',[AdminController::class,'approveUser'])->name('admin.approve');
    Route::get('/reject/{id}',[AdminController::class,'rejectUser'])->name('admin.reject');
    Route::get('/toggle/{id}',[AdminController::class,'toggleStatus'])->name('admin.toggle');
});

