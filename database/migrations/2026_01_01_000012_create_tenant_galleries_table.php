<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Foto galeri tambahan untuk halaman profil publik BLK (kegiatan, dokumentasi), di luar
// logo/cover_image di tabel tenants dan foto per-fasilitas di tabel facilities.
return new class extends Migration {
    public function up(): void {
        Schema::create('tenant_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('photo');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('tenant_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('tenant_galleries');
    }
};
