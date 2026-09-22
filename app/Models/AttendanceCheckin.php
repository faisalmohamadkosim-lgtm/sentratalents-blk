<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

// QR dinamis untuk absensi -- diadopsi dari alur antrian QR klinikita (PatientCheckin),
// hasil scan langsung mengisi Attendance.clock_in/clock_out (bukan nomor antrian).
class AttendanceCheckin extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'employee_id', 'date', 'clock_type', 'token', 'qr_shown_at',
        'status', 'scanned_by', 'scanned_at', 'attendance_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'qr_shown_at' => 'datetime',
            'scanned_at' => 'datetime',
        ];
    }

    // Anti-joki -- sama seperti PatientCheckin klinikita: QR wajib baru ditampilkan/
    // di-refresh maksimal sekian detik sebelum discan, supaya bukan cuma screenshot lama.
    public const QR_FRESHNESS_SECONDS = 60;

    public function isQrFresh(): bool
    {
        return $this->qr_shown_at && $this->qr_shown_at->diffInSeconds(now()) <= self::QR_FRESHNESS_SECONDS;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function employee() { return $this->belongsTo(Employee::class); }
    public function scanner() { return $this->belongsTo(User::class, 'scanned_by'); }
    public function attendance() { return $this->belongsTo(Attendance::class); }

    public function clockTypeLabel(): string
    {
        return $this->clock_type === 'masuk' ? 'Masuk' : 'Pulang';
    }

    /**
     * Proses hasil scan -- buat sesi Attendance baru untuk "masuk", atau tutup sesi
     * terbuka untuk "pulang" (satu hari boleh lebih dari satu sesi). Pemanggil WAJIB
     * sudah memvalidasi isPending()/isQrFresh() lebih dulu. Dipakai baik oleh
     * AttendanceScanController (scan oleh role security yang login) maupun
     * PublicAttendanceScanController (kiosk publik tanpa login) -- satu-satunya beda
     * di antara keduanya cuma siapa `scanned_by`.
     *
     * @return Attendance|string Attendance kalau berhasil, pesan error kalau gagal.
     */
    public function processScan(?int $scannedBy): Attendance|string
    {
        $openSession = Attendance::withoutGlobalScope('tenant')
            ->where('tenant_id', $this->tenant_id)
            ->where('employee_id', $this->employee_id)
            ->whereDate('date', $this->date)
            ->whereNotNull('clock_in')->whereNull('clock_out')
            ->latest('id')->first();

        if ($this->clock_type === 'masuk') {
            if ($openSession) {
                return 'Karyawan ini masih dalam sesi absen yang belum pulang.';
            }
            $attendance = Attendance::create([
                'tenant_id' => $this->tenant_id,
                'employee_id' => $this->employee_id,
                'date' => $this->date,
                'status' => 'hadir',
                'clock_in' => now(),
            ]);
        } else {
            if (! $openSession) {
                return 'Karyawan ini belum absen masuk.';
            }
            $attendance = $openSession;
            $attendance->clock_out = now();
            $attendance->save();
        }

        $this->status = 'scanned';
        $this->scanned_by = $scannedBy;
        $this->scanned_at = now();
        $this->attendance_id = $attendance->id;
        $this->save();

        return $attendance;
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
