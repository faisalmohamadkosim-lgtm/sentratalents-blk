<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Services\PayrollCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $periods = PayrollPeriod::withCount('items')->with('items')
            ->orderByDesc('period_year')->orderByDesc('period_month')->paginate(12);
        return view('payroll.index', compact('periods'));
    }

    public function create()
    {
        return view('payroll.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
        ]);

        if (PayrollPeriod::where('period_month', $data['period_month'])->where('period_year', $data['period_year'])->exists()) {
            return back()->with('error', 'Periode payroll bulan ini sudah pernah dibuat.');
        }

        $cfg = auth()->user()->tenant;
        $period = DB::transaction(function () use ($data, $cfg) {
            $period = PayrollPeriod::create([
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
                'status' => 'draft',
                'generated_by' => auth()->id(),
                'generated_at' => now(),
            ]);

            $employees = Employee::where('status', 'active')->get();
            foreach ($employees as $employee) {
                $attendance = $this->attendanceSummary($employee->id, $data['period_month'], $data['period_year']);
                $calc = PayrollCalculator::calculate($employee, $attendance, $cfg);
                $calc['payroll_period_id'] = $period->id;
                $calc['employee_id'] = $employee->id;
                PayrollItem::create($calc);
            }

            return $period;
        });

        return redirect()->route('payroll.show', $period)->with('success', 'Payroll ' . $period->label() . ' berhasil digenerate untuk ' . $period->items()->count() . ' pegawai.');
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        $payrollPeriod->load('items.employee', 'generatedBy', 'finalizedBy');
        return view('payroll.show', ['period' => $payrollPeriod]);
    }

    /** Edit potongan lain-lain manual (misal kasbon) untuk seluruh pegawai sekaligus, sebelum periode difinalisasi. */
    public function updateDeductions(Request $request, PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->isFinalized()) {
            return back()->with('error', 'Periode ini sudah difinalisasi, tidak dapat diubah.');
        }
        $data = $request->validate([
            'other_deduction' => 'nullable|array',
            'other_deduction.*' => 'nullable|numeric|min:0',
            'other_deduction_notes' => 'nullable|array',
            'other_deduction_notes.*' => 'nullable|string|max:255',
        ]);

        foreach ($payrollPeriod->items as $item) {
            $amount = (float) ($data['other_deduction'][$item->id] ?? 0);
            $notes = $data['other_deduction_notes'][$item->id] ?? null;
            $updated = PayrollCalculator::recalculateWithOtherDeduction($item->toArray(), $amount);
            $item->update([
                'other_deduction' => $amount,
                'other_deduction_notes' => $notes,
                'total_deduction' => $updated['total_deduction'],
                'net_salary' => $updated['net_salary'],
            ]);
        }

        return back()->with('success', 'Potongan lain-lain diperbarui.');
    }

    public function finalize(PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->isFinalized()) {
            return back()->with('error', 'Periode ini sudah difinalisasi.');
        }
        $payrollPeriod->update(['status' => 'finalized', 'finalized_by' => auth()->id(), 'finalized_at' => now()]);
        return back()->with('success', 'Payroll ' . $payrollPeriod->label() . ' difinalisasi. Siap dibayarkan.');
    }

    public function payItem(Request $request, PayrollPeriod $payrollPeriod, PayrollItem $item)
    {
        abort_unless($item->payroll_period_id === $payrollPeriod->id, 404);
        if (! $payrollPeriod->isFinalized()) {
            return back()->with('error', 'Periode ini belum difinalisasi.');
        }
        if ($item->isPaid()) {
            return back()->with('error', 'Gaji pegawai ini sudah tercatat dibayar.');
        }
        $data = $request->validate([
            'payment_method' => 'required|in:transfer,tunai',
            'paid_date' => 'required|date',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $proofPath = $request->hasFile('payment_proof') ? $request->file('payment_proof')->store('payroll-proofs', 'public') : null;
        $item->update([
            'status' => 'paid',
            'payment_method' => $data['payment_method'],
            'paid_date' => $data['paid_date'],
            'payment_proof' => $proofPath,
            'paid_by' => auth()->id(),
        ]);
        return back()->with('success', 'Gaji ' . $item->employee_name . ' tercatat dibayar.');
    }

    public function payslipPdf(PayrollItem $item)
    {
        $user = auth()->user();
        $employee = Employee::find($item->employee_id);
        $isOwnPayslip = $employee && $employee->user_id === $user->id;
        abort_unless($isOwnPayslip || in_array($user->role, ['admin', 'owner']), 403);

        $item->load('period');
        $cfg = $user->tenant;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.payslip-pdf', ['item' => $item, 'cfg' => $cfg]);
        return $pdf->download('Slip-Gaji-' . str_replace(' ', '-', $item->employee_name) . '-' . $item->period->label() . '.pdf');
    }

    public function myPayslips()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $items = $employee
            ? PayrollItem::with('period')->where('employee_id', $employee->id)
                ->whereHas('period', fn ($q) => $q->where('status', 'finalized'))
                ->orderByDesc('id')->get()
            : collect();
        return view('payroll.mine', compact('employee', 'items'));
    }

    private function attendanceSummary(int $employeeId, int $month, int $year): array
    {
        // COUNT(DISTINCT date), bukan COUNT(*) -- satu tanggal kini bisa punya lebih dari satu
        // sesi absen (masuk/pulang), jadi hitung per-hari bukan per-sesi supaya tidak dobel.
        $counts = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)->whereMonth('date', $month)
            ->selectRaw('status, COUNT(DISTINCT date) as total, COALESCE(SUM(overtime_hours),0) as overtime')
            ->groupBy('status')->get()->keyBy('status');

        return [
            'present_days' => (int) ($counts['hadir']->total ?? 0),
            'leave_days' => (int) ($counts['cuti']->total ?? 0) + (int) ($counts['izin']->total ?? 0),
            'sick_days' => (int) ($counts['sakit']->total ?? 0),
            'absent_days' => (int) ($counts['alpha']->total ?? 0),
            'overtime_hours' => (float) $counts->sum('overtime'),
        ];
    }
}
