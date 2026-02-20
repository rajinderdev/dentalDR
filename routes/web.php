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
    Route::get('/admin/doctors/data', [App\Http\Controllers\ManageDoctorController::class, 'getDoctorsData'])->name('admin.doctors.data');
    Route::get('/admin/doctors/create', [App\Http\Controllers\ManageDoctorController::class, 'create'])->name('admin.doctors.create');
    Route::post('/admin/doctors', [App\Http\Controllers\ManageDoctorController::class, 'store'])->name('admin.doctors.store');
    Route::get('/admin/doctors/{id}/edit', [App\Http\Controllers\ManageDoctorController::class, 'edit'])->name('admin.doctors.edit');
    Route::put('/admin/doctors/{id}', [App\Http\Controllers\ManageDoctorController::class, 'update'])->name('admin.doctors.update');
    Route::delete('/admin/doctors/{id}', [App\Http\Controllers\ManageDoctorController::class, 'destroy'])->name('admin.doctors.destroy');

    // Manage Clinic routes
    Route::get('/admin/clinic', [App\Http\Controllers\ManageClinicController::class, 'index'])->name('admin.clinic.index');
    Route::get('/admin/clinic/data', [App\Http\Controllers\ManageClinicController::class, 'getClinicsData'])->name('admin.clinic.data');
    Route::get('/admin/clinic/create', [App\Http\Controllers\ManageClinicController::class, 'create'])->name('admin.clinic.create');
    Route::get('/admin/clinic/states/{countryId}', [App\Http\Controllers\ManageClinicController::class, 'getStatesByCountry'])->name('admin.clinic.getStatesByCountry');
    Route::post('/admin/clinic', [App\Http\Controllers\ManageClinicController::class, 'store'])->name('admin.clinic.store');
    Route::get('/admin/clinic/{id}/edit', [App\Http\Controllers\ManageClinicController::class, 'edit'])->name('admin.clinic.edit');
    Route::put('/admin/clinic/{id}', [App\Http\Controllers\ManageClinicController::class, 'update'])->name('admin.clinic.update');
    Route::delete('/admin/clinic/{id}', [App\Http\Controllers\ManageClinicController::class, 'destroy'])->name('admin.clinic.destroy');

    // Manage Chairs routes
    Route::get('/admin/chairs', [App\Http\Controllers\ManageChairController::class, 'index'])->name('admin.chairs.index');
    Route::get('/admin/chairs/create', [App\Http\Controllers\ManageChairController::class, 'create'])->name('admin.chairs.create');
    Route::post('/admin/chairs', [App\Http\Controllers\ManageChairController::class, 'store'])->name('admin.chairs.store');
    Route::get('/admin/chairs/{id}/edit', [App\Http\Controllers\ManageChairController::class, 'edit'])->name('admin.chairs.edit');
    Route::put('/admin/chairs/{id}', [App\Http\Controllers\ManageChairController::class, 'update'])->name('admin.chairs.update');
    Route::delete('/admin/chairs/{id}', [App\Http\Controllers\ManageChairController::class, 'destroy'])->name('admin.chairs.destroy');
    Route::post('/admin/chairs/bulk-delete', [App\Http\Controllers\ManageChairController::class, 'bulkDelete'])->name('admin.chairs.bulk-delete');

    // Manage LookUps routes
    Route::get('/admin/lookups', [App\Http\Controllers\ManageLookUpController::class, 'index'])->name('admin.lookups.index');
    Route::get('/admin/lookups/create', [App\Http\Controllers\ManageLookUpController::class, 'create'])->name('admin.lookups.create');
    Route::post('/admin/lookups', [App\Http\Controllers\ManageLookUpController::class, 'store'])->name('admin.lookups.store');
    Route::get('/admin/lookups/{id}/edit', [App\Http\Controllers\ManageLookUpController::class, 'edit'])->name('admin.lookups.edit');
    Route::put('/admin/lookups/{id}', [App\Http\Controllers\ManageLookUpController::class, 'update'])->name('admin.lookups.update');
    Route::delete('/admin/lookups/{id}', [App\Http\Controllers\ManageLookUpController::class, 'destroy'])->name('admin.lookups.destroy');
    Route::post('/admin/lookups/bulk-delete', [App\Http\Controllers\ManageLookUpController::class, 'bulkDelete'])->name('admin.lookups.bulk-delete');

    // Manage Medicine routes
    Route::get('/admin/medicines', [App\Http\Controllers\ManageMedicineController::class, 'index'])->name('admin.medicines.index');
    Route::get('/admin/medicines/create', [App\Http\Controllers\ManageMedicineController::class, 'create'])->name('admin.medicines.create');
    Route::post('/admin/medicines', [App\Http\Controllers\ManageMedicineController::class, 'store'])->name('admin.medicines.store');
    Route::get('/admin/medicines/{id}/edit', [App\Http\Controllers\ManageMedicineController::class, 'edit'])->name('admin.medicines.edit');
    Route::put('/admin/medicines/{id}', [App\Http\Controllers\ManageMedicineController::class, 'update'])->name('admin.medicines.update');
    Route::delete('/admin/medicines/{id}', [App\Http\Controllers\ManageMedicineController::class, 'destroy'])->name('admin.medicines.destroy');
    Route::post('/admin/medicines/bulk-delete', [App\Http\Controllers\ManageMedicineController::class, 'bulkDelete'])->name('admin.medicines.bulk-delete');

    // Manage Rx-Template routes
    Route::get('/admin/rx-templates', [App\Http\Controllers\ManageRxTemplateController::class, 'index'])->name('admin.rx-templates.index');
    Route::get('/admin/rx-templates/create', [App\Http\Controllers\ManageRxTemplateController::class, 'create'])->name('admin.rx-templates.create');
    Route::post('/admin/rx-templates', [App\Http\Controllers\ManageRxTemplateController::class, 'store'])->name('admin.rx-templates.store');
    Route::get('/admin/rx-templates/{id}/edit', [App\Http\Controllers\ManageRxTemplateController::class, 'edit'])->name('admin.rx-templates.edit');
    Route::put('/admin/rx-templates/{id}', [App\Http\Controllers\ManageRxTemplateController::class, 'update'])->name('admin.rx-templates.update');
    Route::delete('/admin/rx-templates/{id}', [App\Http\Controllers\ManageRxTemplateController::class, 'destroy'])->name('admin.rx-templates.destroy');
    Route::post('/admin/rx-templates/bulk-delete', [App\Http\Controllers\ManageRxTemplateController::class, 'bulkDelete'])->name('admin.rx-templates.bulk-delete');
    Route::get('/admin/rx-templates/search-medicines', [App\Http\Controllers\ManageRxTemplateController::class, 'searchMedicines'])->name('admin.rx-templates.search-medicines');

    // Manage Clinic Attributes routes
    Route::get('/admin/clinic-attributes', [App\Http\Controllers\ManageClinicAttributeController::class, 'index'])->name('admin.clinic-attributes.index');
    Route::post('/admin/clinic-attributes/update-value', [App\Http\Controllers\ManageClinicAttributeController::class, 'updateValue'])->name('admin.clinic-attributes.update-value');

    // Manage Communication Attributes routes
    Route::get('/admin/communication-attributes', [App\Http\Controllers\ManageCommunicationAttributeController::class, 'index'])->name('admin.communication-attributes.index');
    Route::post('/admin/communication-attributes', [App\Http\Controllers\ManageCommunicationAttributeController::class, 'update'])->name('admin.communication-attributes.update');

    // Manage DocType routes
    Route::get('/admin/doc-types', [App\Http\Controllers\ManageDocTypeController::class, 'index'])->name('admin.doc-types.index');
    Route::post('/admin/doc-types', [App\Http\Controllers\ManageDocTypeController::class, 'store'])->name('admin.doc-types.store');
    Route::get('/admin/doc-types/{id}/edit', [App\Http\Controllers\ManageDocTypeController::class, 'edit'])->name('admin.doc-types.edit');
    Route::put('/admin/doc-types/{id}', [App\Http\Controllers\ManageDocTypeController::class, 'update'])->name('admin.doc-types.update');
    Route::delete('/admin/doc-types/{id}', [App\Http\Controllers\ManageDocTypeController::class, 'destroy'])->name('admin.doc-types.destroy');
    Route::post('/admin/doc-types/bulk-delete', [App\Http\Controllers\ManageDocTypeController::class, 'bulkDelete'])->name('admin.doc-types.bulk-delete');

    // Manage Bank Accounts routes
    Route::get('/admin/bank-accounts', [App\Http\Controllers\ManageBankAccountController::class, 'index'])->name('admin.bank-accounts.index');
    Route::post('/admin/bank-accounts', [App\Http\Controllers\ManageBankAccountController::class, 'store'])->name('admin.bank-accounts.store');
    Route::get('/admin/bank-accounts/{id}/edit', [App\Http\Controllers\ManageBankAccountController::class, 'edit'])->name('admin.bank-accounts.edit');
    Route::put('/admin/bank-accounts/{id}', [App\Http\Controllers\ManageBankAccountController::class, 'update'])->name('admin.bank-accounts.update');
    Route::delete('/admin/bank-accounts/{id}', [App\Http\Controllers\ManageBankAccountController::class, 'destroy'])->name('admin.bank-accounts.destroy');
    Route::post('/admin/bank-accounts/bulk-delete', [App\Http\Controllers\ManageBankAccountController::class, 'bulkDelete'])->name('admin.bank-accounts.bulk-delete');

    // Users Activities route
    Route::get('/admin/user-activities', [App\Http\Controllers\UserActivitiesController::class, 'index'])->name('admin.user-activities.index');

    // Manage Lab Items routes
    Route::get('/admin/lab-items', [App\Http\Controllers\ManageLabItemController::class, 'index'])->name('admin.lab-items.index');
    Route::get('/admin/lab-items/create', [App\Http\Controllers\ManageLabItemController::class, 'create'])->name('admin.lab-items.create');
    Route::post('/admin/lab-items', [App\Http\Controllers\ManageLabItemController::class, 'store'])->name('admin.lab-items.store');
    Route::get('/admin/lab-items/{id}/edit', [App\Http\Controllers\ManageLabItemController::class, 'edit'])->name('admin.lab-items.edit');
    Route::put('/admin/lab-items/{id}', [App\Http\Controllers\ManageLabItemController::class, 'update'])->name('admin.lab-items.update');
    Route::delete('/admin/lab-items/{id}', [App\Http\Controllers\ManageLabItemController::class, 'destroy'])->name('admin.lab-items.destroy');
    Route::post('/admin/lab-items/bulk-delete', [App\Http\Controllers\ManageLabItemController::class, 'bulkDelete'])->name('admin.lab-items.bulk-delete');
});