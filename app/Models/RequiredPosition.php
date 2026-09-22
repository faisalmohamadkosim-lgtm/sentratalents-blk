<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class RequiredPosition extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'position_name', 'min_count', 'notes', 'active', 'created_by'];

    protected $casts = ['min_count' => 'integer', 'active' => 'boolean'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Jumlah terpenuhi -- dicocokkan dari nama jabatan ke data Karyawan aktif (pencocokan
     * sebagian, tidak case-sensitive), ditambah akun Trainer/Instruktur aktif jika nama
     * jabatan mengandung kata "instruktur"/"trainer" (karena instruktur publik dikelola
     * lewat model Trainer terpisah, tidak selalu tercatat sebagai Karyawan/Employee).
     */
    public function fulfilledCount(): int
    {
        $count = Employee::where('status', 'active')
            ->where('position', 'like', '%' . $this->position_name . '%')
            ->count();

        $positionNameLower = mb_strtolower($this->position_name);
        if (str_contains($positionNameLower, 'instruktur') || str_contains($positionNameLower, 'trainer')) {
            $count += Trainer::where('active', true)->count();
        }

        return $count;
    }

    public function isFulfilled(): bool
    {
        return $this->fulfilledCount() >= $this->min_count;
    }

    /** Template contoh umum posisi minimal BLK di Indonesia -- bukan nasihat hukum/perizinan resmi. */
    public static function defaultTemplate(): array
    {
        return [
            ['position_name' => 'Kepala BLK', 'min_count' => 1, 'notes' => 'Penanggung jawab penyelenggaraan pelatihan di BLK.'],
            ['position_name' => 'Instruktur/Trainer', 'min_count' => 2, 'notes' => 'Sesuai jumlah program pelatihan aktif.'],
            ['position_name' => 'Kepala Tata Usaha', 'min_count' => 1, 'notes' => null],
            ['position_name' => 'Tenaga Administrasi', 'min_count' => 1, 'notes' => null],
            ['position_name' => 'Staf Keuangan', 'min_count' => 1, 'notes' => null],
        ];
    }
}
