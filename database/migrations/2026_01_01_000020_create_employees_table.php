<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Akun login terkait, jika ada');
            $table->string('employee_no', 30)->unique();
            $table->string('name', 150);
            $table->string('nik', 20)->nullable()->comment('No. KTP');
            $table->string('npwp', 30)->nullable();
            $table->string('position', 100)->comment('Jabatan');
            $table->string('department', 100)->nullable();
            $table->string('employment_type', 15)->default('tetap')->comment('tetap|kontrak|harian|paruh_waktu');
            $table->string('ptkp_status', 10)->default('TK/0')->comment('TK/0..3, K/0..3 -- dasar hitung PPh21');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->string('status', 15)->default('active')->comment('active|inactive|resigned');
            $table->decimal('base_salary', 12, 2)->default(0)->comment('Gaji pokok');
            $table->decimal('allowance_position', 12, 2)->default(0)->comment('Tunjangan jabatan');
            $table->decimal('allowance_transport', 12, 2)->default(0)->comment('Tunjangan transport');
            $table->decimal('allowance_meal', 12, 2)->default(0)->comment('Tunjangan makan');
            $table->decimal('allowance_other', 12, 2)->default(0)->comment('Tunjangan lain-lain');
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 100)->nullable();
            $table->string('bpjs_kesehatan_no', 30)->nullable();
            $table->string('bpjs_tk_no', 30)->nullable();
            $table->boolean('bpjs_kesehatan_active')->default(true);
            $table->boolean('bpjs_tk_active')->default(true);
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
