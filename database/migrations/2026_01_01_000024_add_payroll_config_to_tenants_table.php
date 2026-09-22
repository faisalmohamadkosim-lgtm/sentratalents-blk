<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('bpjs_kesehatan_rate_employee', 5, 2)->default(1.00)->after('active');
            $table->decimal('bpjs_kesehatan_rate_employer', 5, 2)->default(4.00);
            $table->decimal('bpjs_kesehatan_salary_cap', 12, 2)->default(12000000);
            $table->decimal('bpjs_jht_rate_employee', 5, 2)->default(2.00);
            $table->decimal('bpjs_jht_rate_employer', 5, 2)->default(3.70);
            $table->decimal('bpjs_jp_rate_employee', 5, 2)->default(1.00);
            $table->decimal('bpjs_jp_rate_employer', 5, 2)->default(2.00);
            $table->decimal('bpjs_jp_salary_cap', 12, 2)->default(10042300);
            $table->decimal('bpjs_jkk_rate_employer', 5, 2)->default(0.24);
            $table->decimal('bpjs_jkm_rate_employer', 5, 2)->default(0.30);
            $table->decimal('overtime_multiplier', 5, 2)->default(1.50);
            $table->unsignedTinyInteger('standard_work_days')->default(25)->comment('Hari kerja standar per bulan untuk hitung potongan absen & lembur');
        });
    }

    public function down(): void {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_kesehatan_rate_employee', 'bpjs_kesehatan_rate_employer', 'bpjs_kesehatan_salary_cap',
                'bpjs_jht_rate_employee', 'bpjs_jht_rate_employer',
                'bpjs_jp_rate_employee', 'bpjs_jp_rate_employer', 'bpjs_jp_salary_cap',
                'bpjs_jkk_rate_employer', 'bpjs_jkm_rate_employer',
                'overtime_multiplier', 'standard_work_days',
            ]);
        });
    }
};
