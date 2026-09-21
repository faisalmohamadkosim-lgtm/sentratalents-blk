<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tahapan/kompetensi yang harus dikuasai peserta dalam satu program, berurutan (sort_order).
// Dipakai buat menampilkan kurikulum di halaman profil publik program.
return new class extends Migration {
    public function up(): void {
        Schema::create('program_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('program_stages');
    }
};
