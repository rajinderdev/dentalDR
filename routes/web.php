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
    
    // Manage Users routes
    Route::get('/admin/users', [App\Http\Controllers\ManageUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [App\Http\Controllers\ManageUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\ManageUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [App\Http\Controllers\ManageUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [App\Http\Controllers\ManageUserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/change-password', [App\Http\Controllers\ManageUserController::class, 'changePassword'])->name('admin.users.change-password');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\ManageUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{id}/toggle-lock', [App\Http\Controllers\ManageUserController::class, 'toggleLock'])->name('admin.users.toggle-lock');
    Route::post('/admin/users/{id}/toggle-approval', [App\Http\Controllers\ManageUserController::class, 'toggleApproval'])->name('admin.users.toggle-approval');
});