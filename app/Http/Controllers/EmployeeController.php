<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RequiredPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /** Posisi umum yang selalu ditawarkan, walau belum pernah dipakai karyawan/Jabatan Minimal manapun. */
    private const BASELINE_POSITIONS = [
        'Kepala BLK', 'Kepala Tata Usaha', 'Kepala Bidang Pelatihan', 'Kepala Bidang Sertifikasi',
        'Instruktur/Trainer', 'Tenaga Administrasi', 'Staf Keuangan',
        'Teknisi Bengkel/Laboratorium', 'Petugas Perpustakaan',
        'Petugas Kebersihan', 'Security/Satpam',
    ];

    public function index(Request $request)
    {
        $search = $request->q;
        $status = $request->status;
        $employees = Employee::when($search, fn ($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('employee_no', 'like', "%$search%")
                ->orWhere('position', 'like', "%$search%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('name')->paginate(20)->withQueryString();
        return view('employees.index', compact('employees', 'search', 'status'));
    }

    public function create()
    {
        $users = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)
            ->whereDoesntHave('employee')->orderBy('name')->get();
        $positionOptions = $this->positionOptions();
        return view('employees.form', compact('users', 'positionOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['employee_no'] = $this->generateNo();
        $data['created_by'] = auth()->id();
        $data = $this->handlePhoto($request, $data);
        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Karyawan ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $users = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)
            ->where(fn ($q) => $q->whereDoesntHave('employee')->orWhere('id', $employee->user_id))
            ->orderBy('name')->get();
        $positionOptions = $this->positionOptions();
        return view('employees.form', compact('employee', 'users', 'positionOptions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $this->validated($request, $employee);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($employee->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $employee->photo);
        }
        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Data karyawan diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->payrollItems()->exists()) {
            return back()->with('error', 'Karyawan ini sudah punya riwayat payroll dan tidak dapat dihapus. Ubah status jadi Non-aktif/Resign saja.');
        }
        $this->deletePhoto($employee->photo);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan dihapus.');
    }

    private function validated(Request $request, ?Employee $employee = null): array
    {
        $userIdRule = 'nullable|exists:users,id';
        $data = $request->validate([
            'user_id' => $userIdRule,
            'name' => 'required|string|max:150',
            'nik' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'position' => 'required|string|max:100',
            'position_other' => 'required_if:position,__other__|nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'employment_type' => 'required|in:tetap,kontrak,harian,paruh_waktu',
            'ptkp_status' => 'required|in:' . implode(',', Employee::ptkpOptions()),
            'join_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,resigned',
            'base_salary' => 'required|numeric|min:0',
            'allowance_position' => 'nullable|numeric|min:0',
            'allowance_transport' => 'nullable|numeric|min:0',
            'allowance_meal' => 'nullable|numeric|min:0',
            'allowance_other' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:100',
            'bpjs_kesehatan_no' => 'nullable|string|max:30',
            'bpjs_tk_no' => 'nullable|string|max:30',
            'bpjs_kesehatan_active' => 'sometimes|boolean',
            'bpjs_tk_active' => 'sometimes|boolean',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($data['position'] === '__other__') {
            $data['position'] = $data['position_other'];
        }
        unset($data['position_other']);

        return $data;
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        } else {
            unset($data['photo']);
        }
        return $data;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Gabungan posisi baku + Jabatan Minimal + posisi yang sudah dipakai karyawan, unik & terurut. */
    private function positionOptions(): array
    {
        $used = Employee::whereNotNull('position')->distinct()->pluck('position')->all();
        $required = RequiredPosition::pluck('position_name')->all();
        $all = array_unique(array_merge(self::BASELINE_POSITIONS, $required, $used));
        sort($all, SORT_NATURAL | SORT_FLAG_CASE);
        return $all;
    }

    private function generateNo(): string
    {
        $year = now()->year;
        $last = Employee::where('employee_no', 'like', "EMP-{$year}-%")->orderByDesc('id')->value('employee_no');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return "EMP-{$year}-" . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
