<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\OrgStructureMemberController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramStageController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\CompanyPortalController;
use App\Http\Controllers\TenantGalleryController;
use App\Http\Controllers\PublicDirectoryController;
use App\Http\Controllers\PublicTenantController;
use App\Http\Controllers\PublicGraduatesController;
use App\Http\Controllers\Superadmin\AppProfileController as SuperadminAppProfileController;
use App\Http\Controllers\Superadmin\PlatformGalleryController as SuperadminPlatformGalleryController;
use App\Http\Controllers\Superadmin\PlatformNewsController as SuperadminPlatformNewsController;
use App\Http\Controllers\Superadmin\ProfileController as SuperadminProfileController;
use App\Http\Controllers\Superadmin\TenantController as SuperadminTenantController;
use App\Http\Controllers\Superadmin\TenantUserController as SuperadminTenantUserController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Ultraadmin\DashboardController as UltraadminDashboardController;
use App\Http\Controllers\Ultraadmin\SuperadminUserController as UltraadminSuperadminUserController;

// Public routes -- terbuka untuk semua orang termasuk staf yang sedang login, jadi
// sengaja tidak dipasang middleware guest/auth.
Route::get('/', [PublicDirectoryController::class, 'index'])->name('home');
Route::get('/blk/{slug}', [PublicTenantController::class, 'show'])->name('public.tenant.show');
Route::get('/lulusan', [PublicGraduatesController::class, 'index'])->name('public.graduates');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/forgot-password',         [ForgotPasswordController::class, 'showForgot'])->name('password.forgot');
    Route::post('/forgot-password',        [ForgotPasswordController::class, 'sendReset'])->name('password.send');
    Route::get('/reset-password/{token}',  [ForgotPasswordController::class, 'showReset'])->name('password.reset.form');
    Route::post('/reset-password',         [ForgotPasswordController::class, 'doReset'])->name('password.reset');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile/password',  [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Profil BLK, Pelatihan, Peserta -- staff/owner/admin BLK
    Route::middleware('role:admin,owner,staff')->group(function () {
        Route::resource('facilities', FacilityController::class)->except(['show']);
        Route::resource('trainers', TrainerController::class)->except(['show']);
        Route::resource('org-structure', OrgStructureMemberController::class)->except(['show']);

        Route::resource('programs', ProgramController::class);
        Route::post('/programs/{program}/stages',              [ProgramStageController::class, 'store'])->name('programs.stages.store');
        Route::put('/programs/{program}/stages/{stage}',        [ProgramStageController::class, 'update'])->name('programs.stages.update');
        Route::delete('/programs/{program}/stages/{stage}',     [ProgramStageController::class, 'destroy'])->name('programs.stages.destroy');

        Route::resource('batches', BatchController::class);
        Route::resource('participants', ParticipantController::class);

        Route::resource('enrollments', EnrollmentController::class)->except(['show']);
        Route::post('/enrollments/{enrollment}/graduate', [EnrollmentController::class, 'graduate'])->name('enrollments.graduate');

        Route::resource('gallery', TenantGalleryController::class)->only(['index', 'store', 'destroy']);

        Route::resource('companies', CompanyController::class)->except(['show']);
        Route::get('/companies/{company}/users/create',  [CompanyUserController::class, 'create'])->name('companies.users.create');
        Route::post('/companies/{company}/users',         [CompanyUserController::class, 'store'])->name('companies.users.store');
    });

    // Portal perusahaan -- read-only, hanya melihat peserta yang dikirim perusahaan sendiri.
    Route::middleware('role:perusahaan')->group(function () {
        Route::get('/portal', [CompanyPortalController::class, 'index'])->name('company-portal.index');
    });

    // Administrasi -- admin/owner
    Route::middleware('role:admin,owner')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

        Route::get('/tenant-profile',  [TenantProfileController::class, 'edit'])->name('tenant-profile.edit');
        Route::put('/tenant-profile', [TenantProfileController::class, 'update'])->name('tenant-profile.update');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::post('/roles/{id}/toggle', [RoleController::class, 'toggle'])->name('roles.toggle');
    });

    // Superadmin only
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('tenants', SuperadminTenantController::class)->except(['destroy', 'show']);
        Route::resource('tenants.users', SuperadminTenantUserController::class)->only(['create', 'store']);

        Route::get('/profile', [SuperadminProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/name', [SuperadminProfileController::class, 'updateName'])->name('profile.name.update');
        Route::post('/profile/password', [SuperadminProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('/app-profile', [SuperadminAppProfileController::class, 'edit'])->name('app-profile.edit');
        Route::put('/app-profile', [SuperadminAppProfileController::class, 'update'])->name('app-profile.update');

        // Galeri landing page utama (direktori BLK) -- platform-level, bukan per-tenant.
        // parameters(['gallery' => 'photo']) -- samakan nama parameter URI dengan nama
        // variabel $photo di PlatformGalleryController, supaya route model binding &
        // route() helper di view konsisten (tanpa ini, route()-nya gagal generate URL).
        Route::resource('gallery', SuperadminPlatformGalleryController::class)
            ->except(['show'])
            ->parameters(['gallery' => 'photo']);

        // Berita/pengumuman landing page utama -- platform-level, bukan per-tenant.
        Route::resource('news', SuperadminPlatformNewsController::class)
            ->except(['show'])
            ->parameters(['news' => 'article']);
    });

    // Ultraadmin: level di atas superadmin, khusus mengelola akun superadmin. Tidak
    // ada akses ke tenant/operasional -- sengaja dipisah dari group role:superadmin.
    Route::middleware('role:ultraadmin')->prefix('ultraadmin')->name('ultraadmin.')->group(function () {
        Route::get('/dashboard', [UltraadminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('superadmin-users', UltraadminSuperadminUserController::class)->only(['index', 'create', 'store']);
    });
});
