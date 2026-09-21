<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Daftar role dasar BLK Connect -- superadmin/ultraadmin mengelola seluruh tenant (BLK),
// admin/owner/staff mengelola satu BLK masing-masing. can_admin dipakai RoleController
// buat keterangan hak akses di menu Role (bukan otorisasi sebenarnya -- itu tetap lewat
// kolom users.role + RoleMiddleware, lihat app/Http/Middleware/RoleMiddleware.php).
return new class extends Migration {
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('label', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('can_admin')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name'=>'superadmin','label'=>'Super Admin','description'=>'Mengelola seluruh tenant BLK di platform ini.','can_admin'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'ultraadmin','label'=>'Ultra Admin','description'=>'Level di atas superadmin -- khusus mengelola akun superadmin.','can_admin'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'admin','label'=>'Admin BLK','description'=>'Mengelola profil, program, dan data BLK-nya sendiri sepenuhnya.','can_admin'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'owner','label'=>'Pimpinan BLK','description'=>'Kepala/pimpinan BLK -- akses setara admin di BLK-nya sendiri.','can_admin'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'staff','label'=>'Staf BLK','description'=>'Input data harian -- peserta, pendaftaran angkatan, dll.','can_admin'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
