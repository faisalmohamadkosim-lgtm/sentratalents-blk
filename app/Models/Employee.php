<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'user_id', 'employee_no', 'name', 'nik', 'npwp', 'position', 'department',
        'employment_type', 'ptkp_status', 'join_date', 'end_date', 'status',
        'base_salary', 'allowance_position', 'allowance_transport', 'allowance_meal', 'allowance_other',
        'phone', 'email', 'address',
        'bank_name', 'bank_account_number', 'bank_account_holder',
        'bpjs_kesehatan_no', 'bpjs_tk_no', 'bpjs_kesehatan_active', 'bpjs_tk_active',
        'photo', 'notes', 'created_by',
    ];

    protected $casts = [
        'join_date' => 'date', 'end_date' => 'date',
        'base_salary' => 'float', 'allowance_position' => 'float', 'allowance_transport' => 'float',
        'allowance_meal' => 'float', 'allowance_other' => 'float',
        'bpjs_kesehatan_active' => 'boolean', 'bpjs_tk_active' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function payrollItems() { return $this->hasMany(PayrollItem::class); }

    public function getGrossMonthlyAttribute(): float
    {
        return (float) ($this->base_salary + $this->allowance_position + $this->allowance_transport
            + $this->allowance_meal + $this->allowance_other);
    }

    public function employmentTypeLabel(): string
    {
        return match ($this->employment_type) {
            'tetap' => 'Karyawan Tetap',
            'kontrak' => 'Kontrak',
            'harian' => 'Harian Lepas',
            'paruh_waktu' => 'Paruh Waktu',
            default => $this->employment_type,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Non-aktif',
            'resigned' => 'Resign',
            default => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'active' => 'badge-green',
            'inactive' => 'badge-gray',
            'resigned' => 'badge-red',
            default => 'badge-gray',
        };
    }

    public static function ptkpOptions(): array
    {
        return ['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/0', 'K/1', 'K/2', 'K/3'];
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}
