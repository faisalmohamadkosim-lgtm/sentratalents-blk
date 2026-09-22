<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Role khusus scan QR absensi (masuk/pulang) karyawan & trainer -- lihat AttendanceScanController.
return new class extends Migration {
    public function up(): void {
        DB::table('roles')->insert([
            'name' => 'security',
            'label' => 'Security',
            'description' => 'Memindai QR absensi karyawan/trainer di lokasi (masuk/pulang).',
            'can_admin' => false,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function down(): void {
        DB::table('roles')->where('name', 'security')->delete();
    }
};
