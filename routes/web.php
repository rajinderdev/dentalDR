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

    // Manage Doctors routes
    Route::get('/admin/doctors', [App\Http\Controllers\ManageDoctorController::class, 'index'])->name('admin.doctors.index');
    Route::get('/admin/doctors/create', [App\Http\Controllers\ManageDoctorController::class, 'create'])->name('admin.doctors.create');
    Route::post('/admin/doctors', [App\Http\Controllers\ManageDoctorController::class, 'store'])->name('admin.doctors.store');
    Route::get('/admin/doctors/{id}/edit', [App\Http\Controllers\ManageDoctorController::class, 'edit'])->name('admin.doctors.edit');
    Route::put('/admin/doctors/{id}', [App\Http\Controllers\ManageDoctorController::class, 'update'])->name('admin.doctors.update');
    Route::delete('/admin/doctors/{id}', [App\Http\Controllers\ManageDoctorController::class, 'destroy'])->name('admin.doctors.destroy');

    // Manage Chairs routes
    Route::get('/admin/chairs', [App\Http\Controllers\ManageChairController::class, 'index'])->name('admin.chairs.index');
    Route::get('/admin/chairs/create', [App\Http\Controllers\ManageChairController::class, 'create'])->name('admin.chairs.create');
    Route::post('/admin/chairs', [App\Http\Controllers\ManageChairController::class, 'store'])->name('admin.chairs.store');
    Route::get('/admin/chairs/{id}/edit', [App\Http\Controllers\ManageChairController::class, 'edit'])->name('admin.chairs.edit');
    Route::put('/admin/chairs/{id}', [App\Http\Controllers\ManageChairController::class, 'update'])->name('admin.chairs.update');
    Route::delete('/admin/chairs/{id}', [App\Http\Controllers\ManageChairController::class, 'destroy'])->name('admin.chairs.destroy');

    // Manage LookUps routes
    Route::get('/admin/lookups', [App\Http\Controllers\ManageLookUpController::class, 'index'])->name('admin.lookups.index');
    Route::get('/admin/lookups/create', [App\Http\Controllers\ManageLookUpController::class, 'create'])->name('admin.lookups.create');
    Route::post('/admin/lookups', [App\Http\Controllers\ManageLookUpController::class, 'store'])->name('admin.lookups.store');
    Route::get('/admin/lookups/{id}/edit', [App\Http\Controllers\ManageLookUpController::class, 'edit'])->name('admin.lookups.edit');
    Route::put('/admin/lookups/{id}', [App\Http\Controllers\ManageLookUpController::class, 'update'])->name('admin.lookups.update');
    Route::delete('/admin/lookups/{id}', [App\Http\Controllers\ManageLookUpController::class, 'destroy'])->name('admin.lookups.destroy');
    Route::post('/admin/lookups/bulk-delete', [App\Http\Controllers\ManageLookUpController::class, 'bulkDelete'])->name('admin.lookups.bulk-delete');

    // Manage Lab Items routes
    Route::get('/admin/lab-items', [App\Http\Controllers\ManageLabItemController::class, 'index'])->name('admin.lab-items.index');
    Route::get('/admin/lab-items/create', [App\Http\Controllers\ManageLabItemController::class, 'create'])->name('admin.lab-items.create');
    Route::post('/admin/lab-items', [App\Http\Controllers\ManageLabItemController::class, 'store'])->name('admin.lab-items.store');
    Route::get('/admin/lab-items/{id}/edit', [App\Http\Controllers\ManageLabItemController::class, 'edit'])->name('admin.lab-items.edit');
    Route::put('/admin/lab-items/{id}', [App\Http\Controllers\ManageLabItemController::class, 'update'])->name('admin.lab-items.update');
    Route::delete('/admin/lab-items/{id}', [App\Http\Controllers\ManageLabItemController::class, 'destroy'])->name('admin.lab-items.destroy');
    Route::post('/admin/lab-items/bulk-delete', [App\Http\Controllers\ManageLabItemController::class, 'bulkDelete'])->name('admin.lab-items.bulk-delete');
});