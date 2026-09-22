<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Beberapa sesi masuk/pulang per hari kini diizinkan, jadi QR checkin lama untuk
        // slot (employee, date, clock_type) yang sama tidak boleh menghalangi QR baru dibuat.
        // Index baru dibuat DULU sebelum unique constraint lama dihapus -- FK employee_id
        // butuh index yang meng-cover employee_id sepanjang waktu (lihat error 1553 di
        // migration attendances yang sama).
        Schema::table('attendance_checkins', function (Blueprint $table) {
            $table->index(['employee_id', 'date', 'clock_type'], 'ac_employee_date_type_idx');
        });
        Schema::table('attendance_checkins', function (Blueprint $table) {
            $table->dropUnique('ac_employee_date_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_checkins', function (Blueprint $table) {
            $table->unique(['employee_id', 'date', 'clock_type'], 'ac_employee_date_type_unique');
        });
        Schema::table('attendance_checkins', function (Blueprint $table) {
            $table->dropIndex('ac_employee_date_type_idx');
        });
    }
};
