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
use App\Http\Controllers\TrainerUserController;
use App\Http\Controllers\TrainerPortalController;
use App\Http\Controllers\TenantGalleryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\PublicAttendanceScanController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RequiredPositionController;
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

// Kiosk scan QR absensi TANPA login -- biasanya scan QR butuh role security, tapi tablet/HP
// yang dipasang permanen di pos jaga tidak praktis kalau harus login tiap kali. Per-tenant
// lewat slug supaya satu kiosk cuma pernah memproses token milik BLK itu sendiri.
Route::get('/blk/{slug}/absensi-scan', [PublicAttendanceScanController::class, 'show'])->name('public.attendance-scan.show');
Route::post('/blk/{slug}/absensi-scan', [PublicAttendanceScanController::class, 'handle'])->name('public.attendance-scan.handle');

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
        Route::get('/trainers/{trainer}/users/create',  [TrainerUserController::class, 'create'])->name('trainers.users.create');
        Route::post('/trainers/{trainer}/users',          [TrainerUserController::class, 'store'])->name('trainers.users.store');
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

        // Slip gaji pribadi -- staf/owner/admin tenant sendiri saja (trainer belum tentu
        // tercatat sebagai Karyawan/payroll; lihat grup absensi di bawah untuk trainer).
        Route::get('/slip-gaji-saya', [PayrollController::class, 'myPayslips'])->name('payroll.mine');
        Route::get('/payroll-items/{item}/slip', [PayrollController::class, 'payslipPdf'])->name('payroll-items.payslip');
    });

    // Absensi pribadi -- staf/owner/admin tenant sendiri DAN trainer (trainer bisa juga
    // tercatat sebagai Karyawan untuk keperluan absensi, ditautkan lewat Employee.user_id).
    // Bukan role:perusahaan (portal eksternal) atau superadmin/ultraadmin (platform-level).
    Route::middleware('role:admin,owner,staff,trainer')->group(function () {
        Route::get('/absensi-saya', [AttendanceController::class, 'mine'])->name('attendances.mine');
        Route::post('/absensi-saya/masuk', [AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
        Route::post('/absensi-saya/pulang', [AttendanceController::class, 'clockOut'])->name('attendances.clock-out');

        // QR absen dinamis -- diadopsi dari alur QR antrian klinikita. Gambar QR & status
        // dipoll dari halaman Absensi Saya, hasil scan diisi oleh role:security lewat
        // AttendanceScanController di bawah.
        Route::get('/absensi-saya/checkin/{checkin}/qr', [AttendanceController::class, 'checkinQr'])->name('attendances.checkin.qr');
        Route::get('/absensi-saya/checkin-status', [AttendanceController::class, 'checkinStatus'])->name('attendances.checkin.status');
    });

    // Portal perusahaan -- read-only, hanya melihat peserta yang dikirim perusahaan sendiri.
    Route::middleware('role:perusahaan')->group(function () {
        Route::get('/portal', [CompanyPortalController::class, 'index'])->name('company-portal.index');
    });

    // Portal trainer -- read-only, hanya melihat program & angkatan yang mereka ampu.
    Route::middleware('role:trainer')->group(function () {
        Route::get('/portal-trainer', [TrainerPortalController::class, 'index'])->name('trainer-portal.index');
        Route::get('/portal-trainer/batches/{batch}', [TrainerPortalController::class, 'batch'])->name('trainer-portal.batch');
    });

    // Scan QR absensi -- role security (+ admin/owner untuk keperluan support/testing),
    // mengadopsi pola security.scan di klinikita.
    Route::middleware('role:security,admin,owner')->group(function () {
        Route::get('/absensi-scan', [AttendanceScanController::class, 'show'])->name('attendance-scan.show');
        Route::post('/absensi-scan', [AttendanceScanController::class, 'handle'])->name('attendance-scan.handle');
    });

    // Administrasi -- admin/owner
    Route::middleware('role:admin,owner')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

        Route::get('/tenant-profile',  [TenantProfileController::class, 'edit'])->name('tenant-profile.edit');
        Route::put('/tenant-profile', [TenantProfileController::class, 'update'])->name('tenant-profile.update');

        // SDM & Payroll -- kelola data karyawan, absensi seluruh pegawai, dan proses gajian.
        // Data gaji sensitif, dibatasi ke admin/owner saja (BLK tidak punya role finance/hr).
        Route::resource('employees', EmployeeController::class)->except(['show']);
        Route::post('/employees/{employee}/reset-device', [AttendanceController::class, 'resetDevice'])->name('employees.reset-device');

        Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
        Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{payrollPeriod}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('/payroll/{payrollPeriod}/deductions', [PayrollController::class, 'updateDeductions'])->name('payroll.deductions.update');
        Route::post('/payroll/{payrollPeriod}/finalize', [PayrollController::class, 'finalize'])->name('payroll.finalize');
        Route::post('/payroll/{payrollPeriod}/items/{item}/pay', [PayrollController::class, 'payItem'])->name('payroll.items.pay');

        // Jabatan Minimal -- checklist posisi minimum yang sebaiknya dimiliki BLK, dicocokkan
        // otomatis ke data Karyawan/Trainer aktif. Level perencanaan SDM, admin/owner saja.
        Route::post('/required-positions/load-defaults', [RequiredPositionController::class, 'loadDefaults'])->name('required-positions.load-defaults');
        Route::resource('required-positions', RequiredPositionController::class)->except(['show']);
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
