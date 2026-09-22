<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// QR dinamis untuk absensi -- diadopsi dari alur antrian QR klinikita (PatientCheckin),
// tapi hasil akhirnya bukan nomor antrian melainkan langsung mengisi Attendance.clock_in/
// clock_out. Satu baris = satu "sesi" QR untuk satu jenis absen (masuk/pulang) pada satu
// tanggal; qr_shown_at dipakai anti-joki (isQrFresh()) sama seperti pola klinikita.
return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_checkins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->string('clock_type', 10)->comment('masuk|pulang');
            $table->string('token', 64)->unique();
            $table->timestamp('qr_shown_at')->nullable();
            $table->string('status', 10)->default('pending')->comment('pending|scanned');
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date', 'clock_type'], 'ac_employee_date_type_unique');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('attendance_id')->references('id')->on('attendances')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attendance_checkins');
    }
};
