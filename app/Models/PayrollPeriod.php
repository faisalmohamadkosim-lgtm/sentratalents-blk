<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'period_month', 'period_year', 'status', 'generated_by', 'generated_at', 'finalized_by', 'finalized_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime', 'finalized_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(PayrollItem::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }
    public function finalizedBy() { return $this->belongsTo(User::class, 'finalized_by'); }

    public function label(): string
    {
        $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        return ($bulan[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function getTotalNetAttribute(): float
    {
        return (float) $this->items->sum('net_salary');
    }

    public function getTotalGrossAttribute(): float
    {
        return (float) $this->items->sum('gross_salary');
    }
}
