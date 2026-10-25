<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// tenant_id null = akun platform (superadmin/ultraadmin), tidak terikat satu BLK.
// tenant_id terisi = akun milik satu BLK (admin/owner/staff), di-scope otomatis lewat
// App\Models\Concerns\BelongsToTenant. Akun bootstrap superadmin/ultraadmin disemai di sini
// karena belum ada tenant sama sekali di titik migrasi ini.
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 50)->default('staff');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Akun dev/testing (khusus di luar produksi). Password-nya tertulis di repo ("Test@123"), jadi di PRODUKSI
        // tidak dibuat: buat akun pertama dengan `php artisan app:bootstrap-ultraadmin` / `app:bootstrap-superadmin`.
        if (! app()->environment('production')) {
            DB::table('users')->insert([
                [
                    'tenant_id' => null,
                    'name' => 'Ultra Admin',
                    'email' => 'ultraadmin@blk.test',
                    'password' => bcrypt('Test@123'),
                    'role' => 'ultraadmin',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tenant_id' => null,
                    'name' => 'Super Admin',
                    'email' => 'superadmin@blk.test',
                    'password' => bcrypt('Test@123'),
                    'role' => 'superadmin',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
    public function down(): void {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
