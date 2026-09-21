<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Role portal read-only untuk perwakilan perusahaan mitra -- lihat companies table & CompanyPortalController.
return new class extends Migration {
    public function up(): void {
        DB::table('roles')->insert([
            'name' => 'perusahaan',
            'label' => 'Perusahaan',
            'description' => 'Perwakilan perusahaan mitra -- memantau status peserta yang dikirim ke BLK.',
            'can_admin' => false,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function down(): void {
        DB::table('roles')->where('name', 'perusahaan')->delete();
    }
};
