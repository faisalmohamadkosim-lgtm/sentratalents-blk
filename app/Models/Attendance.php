<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'date', 'clock_in', 'clock_out', 'status', 'overtime_hours', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'date' => 'date', 'clock_in' => 'datetime', 'clock_out' => 'datetime', 'overtime_hours' => 'float',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            'cuti' => 'Cuti',
            'libur' => 'Libur',
            default => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'hadir' => 'badge-green',
            'izin', 'cuti' => 'badge-blue',
            'sakit' => 'badge-yellow',
            'alpha' => 'badge-red',
            'libur' => 'badge-gray',
            default => 'badge-gray',
        };
    }

    public static function statusOptions(): array
    {
        return ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha', 'cuti' => 'Cuti', 'libur' => 'Libur'];
    }
}
