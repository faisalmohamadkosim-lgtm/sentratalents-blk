<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'payroll_period_id', 'employee_id', 'employee_name', 'position', 'ptkp_status',
        'base_salary', 'allowance_position', 'allowance_transport', 'allowance_meal', 'allowance_other',
        'overtime_hours', 'overtime_amount', 'gross_salary',
        'work_days', 'present_days', 'leave_days', 'sick_days', 'absent_days', 'absence_deduction',
        'bpjs_kesehatan_employee', 'bpjs_jht_employee', 'bpjs_jp_employee',
        'pph21_ter_category', 'pph21_rate', 'pph21_amount',
        'other_deduction', 'other_deduction_notes', 'total_deduction', 'net_salary',
        'bpjs_kesehatan_employer', 'bpjs_jht_employer', 'bpjs_jp_employer', 'bpjs_jkk_employer', 'bpjs_jkm_employer',
        'status', 'payment_method', 'paid_date', 'payment_proof', 'paid_by',
    ];

    protected $casts = [
        'base_salary' => 'float', 'allowance_position' => 'float', 'allowance_transport' => 'float',
        'allowance_meal' => 'float', 'allowance_other' => 'float',
        'overtime_hours' => 'float', 'overtime_amount' => 'float', 'gross_salary' => 'float',
        'absence_deduction' => 'float',
        'bpjs_kesehatan_employee' => 'float', 'bpjs_jht_employee' => 'float', 'bpjs_jp_employee' => 'float',
        'pph21_rate' => 'float', 'pph21_amount' => 'float',
        'other_deduction' => 'float', 'total_deduction' => 'float', 'net_salary' => 'float',
        'bpjs_kesehatan_employer' => 'float', 'bpjs_jht_employer' => 'float', 'bpjs_jp_employer' => 'float',
        'bpjs_jkk_employer' => 'float', 'bpjs_jkm_employer' => 'float',
        'paid_date' => 'date',
    ];

    public function period() { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function paidBy() { return $this->belongsTo(User::class, 'paid_by'); }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function totalEmployerCost(): float
    {
        return (float) ($this->gross_salary
            + $this->bpjs_kesehatan_employer + $this->bpjs_jht_employer
            + $this->bpjs_jp_employer + $this->bpjs_jkk_employer + $this->bpjs_jkm_employer);
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'transfer' => 'Transfer Bank',
            'tunai' => 'Tunai',
            default => '-',
        };
    }
}
