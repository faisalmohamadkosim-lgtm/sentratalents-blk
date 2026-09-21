<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Satu baris = satu BLK (Balai Latihan Kerja) yang pakai platform ini -- dasar multi-tenant,
// dipakai App\Models\Concerns\BelongsToTenant buat auto-scope semua tabel BLK-spesifik.
// Kolom di sini juga jadi sumber data halaman profil publik tiap BLK (logo, sampul,
// deskripsi/sambutan, dll -- lihat PublicTenantController).
return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 80)->unique()->comment('Dipakai di URL profil publik, mis. /blk/nama-slug');
            $table->string('code', 30)->nullable()->unique()->comment('Kode resmi BLK kalau ada, mis. dari Kemnaker');
            $table->text('description')->nullable()->comment('Profil/sambutan singkat, ditampilkan di halaman publik');
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->year('established_year')->nullable();
            $table->string('accreditation', 50)->nullable()->comment('Status akreditasi, mis. "A", "Terakreditasi"');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tenants');
    }
};
