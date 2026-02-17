<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TermsAndCondtionsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/terms-and-conditions', [TermsAndCondtionsController::class, 'index']);
Route::get('/terms-and-conditions/data/{patientId}', [TermsAndCondtionsController::class, 'getData']);

// Digital Signature Routes
Route::prefix('digital-signature')->group(function () {
    Route::post('/', [App\Http\Controllers\DigitalSignatureController::class, 'store']);
    Route::get('/patient/{patientId}', [App\Http\Controllers\DigitalSignatureController::class, 'getByPatient']);
});
// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Manage Roles routes
    Route::get('/admin/roles', [App\Http\Controllers\ManageRoleController::class, 'index'])->name('admin.roles.index');
    Route::put('/admin/roles', [App\Http\Controllers\ManageRoleController::class, 'update'])->name('admin.roles.update');
});