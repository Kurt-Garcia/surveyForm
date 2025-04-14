<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\SurveyResponseController;
use App\Http\Controllers\UserSurveyController;
use App\Http\Controllers\Auth\ChangePasswordController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

// Password change routes - accessible by both users and admins
Route::middleware(['auth:web,admin'])->group(function () {
    Route::get('/password/change', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'changePassword']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\UserSurveyController::class, 'index'])->name('home');
    Route::get('/index', [App\Http\Controllers\UserSurveyController::class, 'index'])->name('index');
    Route::get('/surveys/{survey}', [App\Http\Controllers\UserSurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [App\Http\Controllers\UserSurveyController::class, 'store'])->name('surveys.store');
    Route::get('/surveys/thankyou', [App\Http\Controllers\UserSurveyController::class, 'thankyou'])->name('surveys.thankyou');
});

Route::post('/submit-survey', [FormController::class, 'store'])->name('survey.submit');
Route::post('/survey-responses', [SurveyResponseController::class, 'store'])->name('survey-responses.store');
Route::get('/thankyou', [UserSurveyController::class, 'thankyou'])->name('survey.thankyou');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Admin management routes
        Route::get('/admins/create', [App\Http\Controllers\Admin\AdminManagementController::class, 'create'])->name('admin.admins.create');
        Route::post('/admins', [App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('admin.admins.store');
        
        Route::resource('surveys', \App\Http\Controllers\Admin\SurveyController::class)->names([
            'index' => 'admin.surveys.index',
            'create' => 'admin.surveys.create',
            'store' => 'admin.surveys.store',
            'show' => 'admin.surveys.show',
            'edit' => 'admin.surveys.edit',
            'update' => 'admin.surveys.update',
            'destroy' => 'admin.surveys.destroy'
        ]);
    });
});
