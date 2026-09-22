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
        // Satu pegawai kini bisa punya lebih dari satu sesi absen (masuk/pulang) per hari.
        // Index baru dibuat DULU sebelum unique constraint lama dihapus -- FK employee_id
        // butuh index yang meng-cover employee_id sepanjang waktu, kalau tidak MySQL menolak
        // drop-nya (error 1553).
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['employee_id', 'date']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_employee_id_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['employee_id', 'date']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['employee_id', 'date']);
        });
    }
};
