<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Device fingerprint (canvas/WebGL/screen/timezone/hardwareConcurrency, di-hash SHA-256 di
// client) -- diadopsi dari pola cscallpro (lihat migrate_device_fingerprint.sql &
// computeDeviceFingerprint() di customer.php/call.php). Dipakai buat "auto scan" absensi
// mandiri tanpa perlu petugas security: absen pertama kali dari akun ini menyimpan
// fingerprint device yang dipakai, absen berikutnya WAJIB dari device yang sama.
// Best-effort, bukan identifier yang mustahil dipalsukan, tapi jauh menyulitkan joki lewat
// berbagi akun ke device lain (lihat AttendanceController::clockIn()/clockOut()).
return new class extends Migration {
    public function up(): void {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('device_fingerprint', 64)->nullable()->after('bpjs_tk_active');
        });
    }
    public function down(): void {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('device_fingerprint');
        });
    }
};
