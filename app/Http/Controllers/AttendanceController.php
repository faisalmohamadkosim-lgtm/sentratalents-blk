<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCheckin;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /** Halaman absensi pribadi -- clock in/out untuk pegawai yang punya akun login. */
    public function mine()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        if (! $employee) {
            return view('attendances.mine', ['employee' => null, 'openSession' => null, 'records' => collect(), 'checkin' => null]);
        }

        // Satu hari kini boleh punya lebih dari satu sesi masuk/pulang -- "sesi terbuka" =
        // sudah absen masuk tapi belum pulang untuk sesi itu.
        $openSession = $this->openSession($employee->id);
        $records = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', now()->year)->whereMonth('date', now()->month)
            ->orderByDesc('date')->orderByDesc('id')->get();

        // QR absen -- masuk kalau tidak ada sesi terbuka, pulang kalau ada. Checkin yang
        // sudah discan TIDAK PERNAH dipakai ulang (unique constraint lama yang menyebabkan
        // QR gagal muncul lagi setelah pulang sudah dihapus) -- selalu pakai checkin pending
        // yang masih ada, atau buat baru kalau belum ada, supaya QR selalu siap untuk sesi berikutnya.
        $clockType = $openSession ? 'pulang' : 'masuk';
        $checkin = AttendanceCheckin::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->where('clock_type', $clockType)
            ->where('status', 'pending')
            ->latest('id')->first();
        if (! $checkin) {
            $checkin = AttendanceCheckin::create([
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
                'clock_type' => $clockType,
                'token' => AttendanceCheckin::generateToken(),
                'status' => 'pending',
            ]);
        }

        return view('attendances.mine', compact('employee', 'openSession', 'records', 'checkin'));
    }

    /**
     * Gambar QR (PNG) yang mengodekan token check-in absensi -- ditunjukkan ke petugas
     * security untuk dipindai. Tiap kali benar-benar diminta (dimuat pertama kali atau
     * di-refresh berkala oleh JS di halaman Absensi Saya), dicatat "qr_shown_at" -- dipakai
     * AttendanceScanController buat menolak scan atas QR yang sudah basi (anti-joki).
     */
    public function checkinQr(AttendanceCheckin $checkin)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        abort_unless($employee && $checkin->employee_id === $employee->id, 404);

        $checkin->update(['qr_shown_at' => now()]);

        $result = (new \Endroid\QrCode\Builder\Builder(data: $checkin->token, size: 320, margin: 10))->build();
        return response($result->getString(), 200)
            ->header('Content-Type', $result->getMimeType())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /** Dipoll dari halaman Absensi Saya supaya notifikasi muncul real-time begitu dipindai security. */
    public function checkinStatus()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $checkin = $employee
            ? AttendanceCheckin::where('employee_id', $employee->id)
                ->whereDate('date', now()->toDateString())
                ->latest('id')->first()
            : null;

        return response()->json([
            'checkin' => $checkin ? [
                'id' => $checkin->id,
                'status' => $checkin->status,
                'clock_type' => $checkin->clock_type,
                'clock_type_label' => $checkin->clockTypeLabel(),
                'time' => $checkin->scanned_at?->format('H:i'),
            ] : null,
        ]);
    }

    /** Sesi absen hari ini yang masih terbuka (sudah masuk, belum pulang) untuk pegawai, kalau ada. */
    private function openSession(int $employeeId): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('clock_in')->whereNull('clock_out')
            ->latest('id')->first();
    }

    public function clockIn(Request $request)
    {
        $employee = Employee::where('user_id', auth()->id())->firstOrFail();
        if ($error = $this->checkDevice($employee, $request)) {
            return back()->with('error', $error);
        }
        if ($this->openSession($employee->id)) {
            return back()->with('error', 'Anda masih dalam sesi absen yang belum pulang. Absen pulang dulu sebelum absen masuk lagi.');
        }
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'hadir',
            'clock_in' => now(),
        ]);
        $this->bindDeviceIfNeeded($employee, $request);
        return back()->with('success', 'Absen masuk tercatat pukul ' . now()->format('H:i') . '.');
    }

    public function clockOut(Request $request)
    {
        $employee = Employee::where('user_id', auth()->id())->firstOrFail();
        if ($error = $this->checkDevice($employee, $request)) {
            return back()->with('error', $error);
        }
        $attendance = $this->openSession($employee->id);
        if (! $attendance) {
            return back()->with('error', 'Anda belum absen masuk.');
        }
        $attendance->clock_out = now();
        $attendance->save();
        $this->bindDeviceIfNeeded($employee, $request);
        return back()->with('success', 'Absen pulang tercatat pukul ' . now()->format('H:i') . '.');
    }

    /**
     * Auto scan mandiri tanpa petugas security -- diadopsi dari device fingerprint cscallpro
     * (canvas/WebGL/screen/timezone/hardwareConcurrency, di-hash SHA-256 di client, lihat
     * computeDeviceFingerprint() di attendances/mine.blade.php). Absen pertama kali dari akun
     * ini menyimpan fingerprint-nya; absen berikutnya WAJIB dari device yang sama, supaya
     * berbagi email+password ke orang lain di device lain otomatis ditolak sistem (joki).
     */
    private function checkDevice(Employee $employee, Request $request): ?string
    {
        $incoming = (string) $request->input('device_fingerprint', '');
        if ($incoming === '') {
            return 'Perangkat tidak dapat dikenali (fingerprint kosong). Coba lagi lewat browser yang mendukung, atau hubungi Admin.';
        }
        if ($employee->device_fingerprint && $employee->device_fingerprint !== $incoming) {
            return 'Absen ditolak: perangkat ini bukan perangkat terdaftar untuk akun Anda. Hubungi Admin/Owner untuk reset perangkat kalau Anda memang berganti HP.';
        }
        return null;
    }

    private function bindDeviceIfNeeded(Employee $employee, Request $request): void
    {
        $incoming = (string) $request->input('device_fingerprint', '');
        if ($incoming !== '' && ! $employee->device_fingerprint) {
            $employee->device_fingerprint = $incoming;
            $employee->save();
        }
    }

    /** Admin/Owner mereset perangkat terdaftar pegawai -- dipakai kalau pegawai ganti HP. */
    public function resetDevice(Employee $employee)
    {
        // Assignment langsung, BUKAN update() -- device_fingerprint sengaja tidak ada di
        // Employee::$fillable (supaya tidak bisa diisi lewat form Karyawan biasa), jadi
        // update(['device_fingerprint' => ...]) didiamkan begitu saja oleh mass-assignment
        // guard Eloquent tanpa error.
        $employee->device_fingerprint = null;
        $employee->save();
        return back()->with('success', 'Perangkat terdaftar ' . $employee->name . ' direset. Absen berikutnya akan mendaftarkan perangkat baru.');
    }

    /** Rekap & kelola absensi seluruh pegawai -- untuk Admin/Owner. */
    public function index(Request $request)
    {
        $month = (int) ($request->month ?: now()->month);
        $year = (int) ($request->year ?: now()->year);
        $employeeId = $request->employee_id;

        $records = Attendance::with('employee')
            ->whereYear('date', $year)->whereMonth('date', $month)
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->orderByDesc('date')->orderBy('employee_id')->paginate(30)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('attendances.index', compact('records', 'employees', 'month', 'year', 'employeeId'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('attendances.form', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $this->combineDateTime($this->validated($request));
        $data['recorded_by'] = auth()->id();
        Attendance::create($data);
        return redirect()->route('attendances.index', ['month' => date('n', strtotime($data['date'])), 'year' => date('Y', strtotime($data['date']))])
            ->with('success', 'Absensi ditambahkan.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('attendances.form', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $this->combineDateTime($this->validated($request));
        $data['recorded_by'] = auth()->id();
        $attendance->update($data);
        return redirect()->route('attendances.index', ['month' => date('n', strtotime($data['date'])), 'year' => date('Y', strtotime($data['date']))])
            ->with('success', 'Absensi diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $month = $attendance->date->month;
        $year = $attendance->date->year;
        $attendance->delete();
        return redirect()->route('attendances.index', compact('month', 'year'))->with('success', 'Absensi dihapus.');
    }

    private function combineDateTime(array $data): array
    {
        $data['clock_in'] = ! empty($data['clock_in']) ? $data['date'] . ' ' . $data['clock_in'] . ':00' : null;
        $data['clock_out'] = ! empty($data['clock_out']) ? $data['date'] . ' ' . $data['clock_out'] . ':00' : null;
        return $data;
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(Attendance::statusOptions())),
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);
    }
}
