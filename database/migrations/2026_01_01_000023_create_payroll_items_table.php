<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('payroll_period_id');
            $table->unsignedBigInteger('employee_id');

            // Snapshot data pegawai saat digenerate, supaya histori gajian tidak berubah
            // kalau data master pegawai diedit belakangan.
            $table->string('employee_name', 150);
            $table->string('position', 100)->nullable();
            $table->string('ptkp_status', 10)->nullable();

            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowance_position', 12, 2)->default(0);
            $table->decimal('allowance_transport', 12, 2)->default(0);
            $table->decimal('allowance_meal', 12, 2)->default(0);
            $table->decimal('allowance_other', 12, 2)->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);

            $table->unsignedSmallInteger('work_days')->default(0);
            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('leave_days')->default(0);
            $table->unsignedSmallInteger('sick_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->decimal('absence_deduction', 12, 2)->default(0);

            $table->decimal('bpjs_kesehatan_employee', 12, 2)->default(0);
            $table->decimal('bpjs_jht_employee', 12, 2)->default(0);
            $table->decimal('bpjs_jp_employee', 12, 2)->default(0);
            $table->string('pph21_ter_category', 5)->nullable()->comment('A|B|C, PMK 168/2023');
            $table->decimal('pph21_rate', 6, 4)->default(0)->comment('Tarif TER terpakai (desimal)');
            $table->decimal('pph21_amount', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->string('other_deduction_notes')->nullable();
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);

            // Biaya perusahaan (informasional -- tidak memotong take-home pegawai)
            $table->decimal('bpjs_kesehatan_employer', 12, 2)->default(0);
            $table->decimal('bpjs_jht_employer', 12, 2)->default(0);
            $table->decimal('bpjs_jp_employer', 12, 2)->default(0);
            $table->decimal('bpjs_jkk_employer', 12, 2)->default(0);
            $table->decimal('bpjs_jkm_employer', 12, 2)->default(0);

            $table->string('status', 10)->default('draft')->comment('draft|paid');
            $table->string('payment_method', 10)->nullable()->comment('transfer|tunai');
            $table->date('paid_date')->nullable();
            $table->string('payment_proof')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id'], 'pi_period_employee_unique');
            $table->foreign('tenant_id', 'pi_tenant_id_foreign')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('payroll_period_id', 'pi_period_id_foreign')->references('id')->on('payroll_periods')->cascadeOnDelete();
            $table->foreign('employee_id', 'pi_employee_id_foreign')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('paid_by', 'pi_paid_by_foreign')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_items');
    }
};
