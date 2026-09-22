<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCheckin;
use App\Models\Tenant;
use Illuminate\Http\Request;

/**
 * Kiosk scan QR absensi TANPA login -- biasanya scan ini butuh role security (lihat
 * AttendanceScanController), tapi untuk tablet/HP yang dipasang permanen di pos jaga/pintu
 * masuk, minta operator login tiap kali tidak praktis. Token QR sendiri (acak 64 hex,
 * berjangka pendek lewat isQrFresh(), plus foto karyawan buat verifikasi visual) sudah jadi
 * kontrol keamanan utamanya -- bukan gerbang login di scanner-nya.
 *
 * Halaman ini per-tenant lewat slug di URL (pola sama dengan PublicTenantController), supaya
 * satu kiosk BLK hanya pernah memproses token milik BLK itu sendiri.
 */
class PublicAttendanceScanController extends Controller
{
    public function show(string $slug)
    {
        $tenant = Tenant::where('slug', $slug)->where('active', true)->firstOrFail();
        return view('public.attendance-scan', compact('tenant'));
    }

    public function handle(Request $request, string $slug)
    {
        $tenant = Tenant::where('slug', $slug)->where('active', true)->firstOrFail();

        $data = $request->validate([
            'token' => 'required|string',
        ]);

        // Tanpa login, global scope tenant otomatis tidak aktif (lihat BelongsToTenant) --
        // where('tenant_id', ...) eksplisit di sini yang membatasi token ke kiosk BLK ini.
        $checkin = AttendanceCheckin::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('token', $data['token'])
            ->with('employee')
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

        // scanned_by null -- kiosk publik, tidak ada operator yang login.
        $result = $checkin->processScan(null);
        if (is_string($result)) {
            return response()->json(['success' => false, 'message' => $result], 422);
        }

        return response()->json([
            'success' => true,
            'employee_name' => $checkin->employee->name,
            'employee_position' => $checkin->employee->position,
            'employee_photo_url' => $checkin->employee->photoUrl(),
            'clock_type' => $checkin->clock_type,
            'clock_type_label' => $checkin->clockTypeLabel(),
            'time' => $checkin->scanned_at->format('H:i'),
        ]);
    }
}
