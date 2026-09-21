<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Perusahaan mitra yang mengirim calon peserta pelatihan ke satu BLK tertentu. Perusahaan
// bisa punya akun login sendiri (role "perusahaan", lihat users.company_id) untuk memantau
// status peserta yang mereka kirim lewat portal read-only (CompanyPortalController).
return new class extends Migration {
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('companies');
    }
};
