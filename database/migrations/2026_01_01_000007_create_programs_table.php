<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Program/kejuruan pelatihan yang diselenggarakan satu BLK, mis. "Teknik Las", "Tata Busana".
// Tahapan pelatihan yang harus dikuasai peserta ada di tabel terpisah program_stages.
return new class extends Migration {
    public function up(): void {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('lead_trainer_id')->nullable();
            $table->string('name');
            $table->string('category', 100)->nullable()->comment('Kejuruan/bidang, mis. "Teknik Manufaktur"');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_days')->nullable()->comment('Lama pelatihan dalam hari');
            $table->unsignedInteger('quota')->nullable()->comment('Kapasitas peserta per angkatan');
            $table->string('photo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->foreign('lead_trainer_id')->references('id')->on('trainers')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('programs');
    }
};
