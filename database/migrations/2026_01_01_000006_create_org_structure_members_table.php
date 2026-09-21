<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Struktur organisasi/kepengurusan BLK (kepala BLK, kepala bidang, dll), terpisah dari
// trainers karena posisi struktural belum tentu mengajar dan trainer belum tentu struktural.
return new class extends Migration {
    public function up(): void {
        Schema::create('org_structure_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('position')->comment('Jabatan, mis. "Kepala BLK", "Kepala Tata Usaha"');
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('tenant_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('org_structure_members');
    }
};
