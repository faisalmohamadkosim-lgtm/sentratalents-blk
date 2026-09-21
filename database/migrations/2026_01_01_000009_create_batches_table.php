<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Satu angkatan/pelaksanaan dari sebuah program, dengan jadwal & kuota sendiri.
// Peserta mendaftar ke satu batch lewat enrollments, bukan langsung ke program.
return new class extends Migration {
    public function up(): void {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('program_id');
            $table->string('code', 50)->comment('Kode angkatan, mis. "2026-A"');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->string('status', 20)->default('planned')->comment('planned|ongoing|completed|cancelled');
            $table->timestamps();

            $table->index('tenant_id');
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('batches');
    }
};
