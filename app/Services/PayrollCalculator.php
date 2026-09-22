<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Tenant;
use App\Support\Pph21TerTable;

class PayrollCalculator
{
    /**
     * Hitung seluruh komponen payroll satu pegawai untuk satu periode.
     * $attendance = ['work_days'=>int,'present_days'=>int,'leave_days'=>int,'sick_days'=>int,'absent_days'=>int,'overtime_hours'=>float]
     * Return array kolom siap disimpan ke PayrollItem (belum termasuk payroll_period_id/employee_id).
     */
    public static function calculate(Employee $employee, array $attendance, Tenant $cfg): array
    {
        $baseSalary = (float) $employee->base_salary;
        $allowancePosition = (float) $employee->allowance_position;
        $allowanceTransport = (float) $employee->allowance_transport;
        $allowanceMeal = (float) $employee->allowance_meal;
        $allowanceOther = (float) $employee->allowance_other;

        $workDays = max(1, (int) ($attendance['work_days'] ?? $cfg->standard_work_days));
        $overtimeHours = (float) ($attendance['overtime_hours'] ?? 0);
        $absentDays = (int) ($attendance['absent_days'] ?? 0);

        // Upah lembur per jam -- pembagi 173 mengikuti standar Kepmenakertrans No. 102/2004,
        // disederhanakan pakai satu pengali (bukan tarif berjenjang jam ke-1/selanjutnya).
        $hourlyRate = $baseSalary / 173;
        $overtimeAmount = round($hourlyRate * (float) $cfg->overtime_multiplier * $overtimeHours);

        // Potongan ketidakhadiran (alpha) berdasarkan hari kerja efektif periode ini.
        $dailyRate = $baseSalary / $workDays;
        $absenceDeduction = round($dailyRate * $absentDays);

        $grossSalary = $baseSalary + $allowancePosition + $allowanceTransport + $allowanceMeal
            + $allowanceOther + $overtimeAmount;

        // BPJS Kesehatan -- dasar upah dibatasi salary cap.
        $bpjsKesUpah = min($grossSalary, (float) $cfg->bpjs_kesehatan_salary_cap);
        $bpjsKesEmployee = $employee->bpjs_kesehatan_active
            ? round($bpjsKesUpah * ((float) $cfg->bpjs_kesehatan_rate_employee / 100))
            : 0;
        $bpjsKesEmployer = $employee->bpjs_kesehatan_active
            ? round($bpjsKesUpah * ((float) $cfg->bpjs_kesehatan_rate_employer / 100))
            : 0;

        // BPJS Ketenagakerjaan -- JHT tanpa cap, JP dibatasi salary cap tersendiri.
        $bpjsJhtEmployee = 0;
        $bpjsJhtEmployer = 0;
        $bpjsJpEmployee = 0;
        $bpjsJpEmployer = 0;
        $bpjsJkkEmployer = 0;
        $bpjsJkmEmployer = 0;
        if ($employee->bpjs_tk_active) {
            $bpjsJhtEmployee = round($grossSalary * ((float) $cfg->bpjs_jht_rate_employee / 100));
            $bpjsJhtEmployer = round($grossSalary * ((float) $cfg->bpjs_jht_rate_employer / 100));
            $bpjsJpUpah = min($grossSalary, (float) $cfg->bpjs_jp_salary_cap);
            $bpjsJpEmployee = round($bpjsJpUpah * ((float) $cfg->bpjs_jp_rate_employee / 100));
            $bpjsJpEmployer = round($bpjsJpUpah * ((float) $cfg->bpjs_jp_rate_employer / 100));
            $bpjsJkkEmployer = round($grossSalary * ((float) $cfg->bpjs_jkk_rate_employer / 100));
            $bpjsJkmEmployer = round($grossSalary * ((float) $cfg->bpjs_jkm_rate_employer / 100));
        }

        // PPh 21 -- estimasi metode TER bulanan (lihat disclaimer di Pph21TerTable).
        $ptkp = $employee->ptkp_status ?: 'TK/0';
        $terCategory = Pph21TerTable::categoryForPtkp($ptkp);
        $pph21Base = max(0, $grossSalary - $absenceDeduction);
        $pph21Rate = Pph21TerTable::rateForGross($terCategory, $pph21Base);
        $pph21Amount = round($pph21Base * $pph21Rate);

        $totalDeduction = $absenceDeduction + $bpjsKesEmployee + $bpjsJhtEmployee + $bpjsJpEmployee + $pph21Amount;
        $netSalary = $grossSalary - $totalDeduction;

        return [
            'employee_name' => $employee->name,
            'position' => $employee->position,
            'ptkp_status' => $ptkp,
            'base_salary' => $baseSalary,
            'allowance_position' => $allowancePosition,
            'allowance_transport' => $allowanceTransport,
            'allowance_meal' => $allowanceMeal,
            'allowance_other' => $allowanceOther,
            'overtime_hours' => $overtimeHours,
            'overtime_amount' => $overtimeAmount,
            'gross_salary' => $grossSalary,
            'work_days' => $workDays,
            'present_days' => (int) ($attendance['present_days'] ?? 0),
            'leave_days' => (int) ($attendance['leave_days'] ?? 0),
            'sick_days' => (int) ($attendance['sick_days'] ?? 0),
            'absent_days' => $absentDays,
            'absence_deduction' => $absenceDeduction,
            'bpjs_kesehatan_employee' => $bpjsKesEmployee,
            'bpjs_jht_employee' => $bpjsJhtEmployee,
            'bpjs_jp_employee' => $bpjsJpEmployee,
            'pph21_ter_category' => $terCategory,
            'pph21_rate' => $pph21Rate,
            'pph21_amount' => $pph21Amount,
            'other_deduction' => 0,
            'other_deduction_notes' => null,
            'total_deduction' => $totalDeduction,
            'net_salary' => $netSalary,
            'bpjs_kesehatan_employer' => $bpjsKesEmployer,
            'bpjs_jht_employer' => $bpjsJhtEmployer,
            'bpjs_jp_employer' => $bpjsJpEmployer,
            'bpjs_jkk_employer' => $bpjsJkkEmployer,
            'bpjs_jkm_employer' => $bpjsJkmEmployer,
            'status' => 'draft',
        ];
    }

    /** Hitung ulang total_deduction & net_salary setelah other_deduction diubah manual. */
    public static function recalculateWithOtherDeduction(array $item, float $otherDeduction): array
    {
        $item['other_deduction'] = $otherDeduction;
        $item['total_deduction'] = $item['absence_deduction'] + $item['bpjs_kesehatan_employee']
            + $item['bpjs_jht_employee'] + $item['bpjs_jp_employee'] + $item['pph21_amount'] + $otherDeduction;
        $item['net_salary'] = $item['gross_salary'] - $item['total_deduction'];
        return $item;
    }
}
