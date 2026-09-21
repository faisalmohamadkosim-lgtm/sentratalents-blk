<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Galeri di landing page utama (direktori BLK) -- BUKAN tenant-scoped, dikelola langsung
// oleh superadmin. link_url opsional: kalau diisi, foto jadi tautan (mis. ke profil satu
// BLK, halaman lulusan, atau URL eksternal); kalau kosong, foto tampil tanpa tautan.
return new class extends Migration {
    public function up(): void {
        Schema::create('platform_gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->string('photo');
            $table->string('caption')->nullable();
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('platform_gallery_photos');
    }
};
