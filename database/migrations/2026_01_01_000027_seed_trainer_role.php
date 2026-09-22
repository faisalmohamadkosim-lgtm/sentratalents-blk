<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Role portal read-only untuk instruktur/trainer -- lihat trainers.user_id & TrainerPortalController.
return new class extends Migration {
    public function up(): void {
        DB::table('roles')->insert([
            'name' => 'trainer',
            'label' => 'Trainer',
            'description' => 'Instruktur/trainer -- memantau program, angkatan, dan peserta yang diampu.',
            'can_admin' => false,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function down(): void {
        DB::table('roles')->where('name', 'trainer')->delete();
    }
};
