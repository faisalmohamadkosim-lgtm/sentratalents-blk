<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Profil instruktur/pelatih satu BLK -- ditampilkan di halaman profil publik dan bisa
// dikaitkan ke program sebagai pengampu utama (programs.lead_trainer_id).
return new class extends Migration {
    public function up(): void {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('specialization')->nullable()->comment('Bidang keahlian, mis. "Las & Fabrikasi Logam"');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('trainers');
    }
};
