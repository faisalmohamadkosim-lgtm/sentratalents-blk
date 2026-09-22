<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCheckin;
use Illuminate\Http\Request;

/**
 * Halaman scan QR absensi oleh petugas security -- memindai token dari QR yang ditunjukkan
 * karyawan/trainer, lalu langsung mengisi Attendance.clock_in/clock_out. Diadopsi dari
 * SecurityScanController klinikita (yang tadinya memberi nomor antrian), disederhanakan
 * jadi absensi: bukan nomor antrian, tapi timestamp masuk/pulang.
 */
class AttendanceScanController extends Controller
{
    public function show()
    {
        return view('attendance-scan.show');
    }

    public function handle(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
        ]);

        $checkin = AttendanceCheckin::with('employee')
            ->where('token', $data['token'])
            ->first();

        if (! $checkin) {
            return response()->json(['success' => false, 'message' => 'QR tidak dikenali.'], 404);
        }
        if (! $checkin->isPending()) {
            return response()->json(['success' => false, 'message' => 'QR ini sudah pernah dipindai.'], 422);
        }
        // Anti-joki: QR wajib baru ditampilkan/di-refresh di halaman Absensi Saya beberapa
        // saat sebelum discan -- kalau sudah basi, minta buka ulang halamannya di lokasi.
        if (! $checkin->isQrFresh()) {
            return response()->json(['success' => false, 'message' => 'QR sudah kedaluwarsa. Minta karyawan membuka ulang halaman Absensi Saya, lalu scan lagi.'], 422);
        }

        $result = $checkin->processScan(auth()->id());
        if (is_string($result)) {
            return response()->json(['success' => false, 'message' => $result], 422);
        }

        return response()->json([
            'success' => true,
            'employee_name' => $checkin->employee->name,
            'employee_position' => $checkin->employee->position,
            // Foto ditampilkan di layar hasil scan supaya petugas security bisa mencocokkan
            // wajah pemegang HP dengan foto karyawan -- backstop manusia untuk kasus akun
            // dibagi ke orang lain (freshness QR sendiri tidak bisa mendeteksi ini).
            'employee_photo_url' => $checkin->employee->photoUrl(),
            'clock_type' => $checkin->clock_type,
            'clock_type_label' => $checkin->clockTypeLabel(),
            'time' => $checkin->scanned_at->format('H:i'),
        ]);
    }
}
