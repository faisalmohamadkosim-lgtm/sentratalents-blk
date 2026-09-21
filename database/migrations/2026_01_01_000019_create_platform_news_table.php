<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Daftar berita/pengumuman di landing page utama -- platform-level (bukan tenant-scoped),
// dikelola langsung oleh superadmin. Ditampilkan sebagai daftar yang bisa digeser
// horizontal (lihat public/directory.blade.php .news-scroll).
return new class extends Migration {
    public function up(): void {
        Schema::create('platform_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->date('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('platform_news');
    }
};
