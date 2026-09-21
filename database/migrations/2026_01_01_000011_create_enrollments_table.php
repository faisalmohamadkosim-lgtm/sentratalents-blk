<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pendaftaran satu peserta ke satu angkatan (batch). status='graduated' + graduation_date
// terisi = peserta ini muncul di daftar lulusan (global maupun per-BLK).
return new class extends Migration {
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('batch_id');
            $table->string('status', 20)->default('registered')->comment('registered|active|graduated|dropped_out');
            $table->decimal('final_score', 5, 2)->nullable();
            $table->date('graduation_date')->nullable();
            $table->string('certificate_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->foreign('participant_id')->references('id')->on('participants')->cascadeOnDelete();
            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};
