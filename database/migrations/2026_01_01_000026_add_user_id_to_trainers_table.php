<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Akun login untuk instruktur/trainer (role "trainer") -- opsional, satu Trainer bisa
// ditautkan ke satu akun User supaya bisa masuk ke portal instruktur (lihat
// TrainerPortalController), memantau program & peserta yang mereka ampu.
return new class extends Migration {
    public function up(): void {
        Schema::table('trainers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('tenant_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
