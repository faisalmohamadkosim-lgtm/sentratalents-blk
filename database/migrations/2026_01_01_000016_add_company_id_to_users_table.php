<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Akun login untuk perwakilan perusahaan (role "perusahaan") -- company_id menautkan akun
// ke satu perusahaan mitra, dipakai CompanyPortalController buat membatasi data yang terlihat.
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('tenant_id');
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
