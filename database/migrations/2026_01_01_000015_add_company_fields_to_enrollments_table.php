<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Peserta bisa didaftarkan mandiri atau dikirim oleh perusahaan mitra -- company_id hanya
// relevan kalau registration_type=perusahaan (lihat App\Models\Company).
return new class extends Migration {
    public function up(): void {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('registration_type', 20)->default('mandiri')->after('batch_id')->comment('mandiri|perusahaan');
            $table->unsignedBigInteger('company_id')->nullable()->after('registration_type');

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['registration_type', 'company_id']);
        });
    }
};
