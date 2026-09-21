<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Data diri peserta pelatihan satu BLK. Tidak ada login/portal peserta di aplikasi ini --
// data diinput oleh staff/admin, dan riwayat kelulusan dilihat lewat enrollments.
return new class extends Migration {
    public function up(): void {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('nik', 30)->nullable();
            $table->string('gender', 10)->nullable()->comment('L|P');
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('education', 50)->nullable()->comment('Pendidikan terakhir, mis. "SMA/SMK"');
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'nik']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('participants');
    }
};
