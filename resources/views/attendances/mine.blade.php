@extends('layouts.app')
@section('title', 'Absensi Saya')
@section('content')

<div class="page-header">
  <div class="page-title">Absensi Saya</div>
</div>

@if(! $employee)
<div class="card">
  <div class="text-muted">Akun Anda belum ditautkan ke data karyawan. Hubungi Admin/Owner untuk menautkan akun Anda ke data karyawan supaya bisa absen.</div>
</div>
@else
<div class="card">
  <div class="card-title">{{ $employee->name }} — {{ $employee->position }}</div>
  <div class="text-muted" style="margin-bottom:1rem">Hari ini, {{ now()->translatedFormat('l, d F Y') }}</div>

  <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap">
    <div>
      <div style="font-size:.72rem;color:var(--text3);text-transform:uppercase">Absen Masuk</div>
      <div style="font-size:1.4rem;font-weight:700">{{ $openSession?->clock_in?->format('H:i') ?? '--:--' }}</div>
    </div>
    <div>
      <div style="font-size:.72rem;color:var(--text3);text-transform:uppercase">Absen Pulang</div>
      <div style="font-size:1.4rem;font-weight:700">--:--</div>
    </div>
    <div style="display:flex;gap:.5rem">
      <form method="POST" action="{{ route('attendances.clock-in') }}" id="clockInForm">
        @csrf
        <input type="hidden" name="device_fingerprint" class="device-fp-field">
        <button type="submit" class="btn btn-success" {{ $openSession ? 'disabled' : '' }}>🟢 Absen Masuk</button>
      </form>
      <form method="POST" action="{{ route('attendances.clock-out') }}" id="clockOutForm">
        @csrf
        <input type="hidden" name="device_fingerprint" class="device-fp-field">
        <button type="submit" class="btn btn-danger" {{ ! $openSession ? 'disabled' : '' }}>🔴 Absen Pulang</button>
      </form>
    </div>
  </div>
  <div class="text-muted" style="margin-top:.75rem">
    Sehari bisa lebih dari satu sesi masuk/pulang -- lihat semua sesi hari ini di tabel Riwayat di bawah.
    @if($employee->device_fingerprint)
      🔒 Absen mandiri (tanpa security) hanya bisa dari perangkat yang sudah terdaftar untuk akun ini.
    @else
      ℹ️ Absen mandiri pertama Anda akan otomatis mendaftarkan perangkat ini untuk akun Anda.
    @endif
  </div>
</div>

@if($checkin)
<div class="card" style="max-width:320px">
  <div class="card-title">📷 Absen via QR — {{ $checkin->clockTypeLabel() }}</div>
  <div class="text-muted" style="margin-bottom:.75rem">Tunjukkan QR ini ke petugas security untuk absen {{ strtolower($checkin->clockTypeLabel()) }} tanpa perlu klik tombol di atas.</div>
  <div style="text-align:center">
    <img id="attendanceQrImg" src="{{ route('attendances.checkin.qr', $checkin) }}" alt="QR Absensi {{ $checkin->clockTypeLabel() }}" style="max-width:220px;width:100%;border-radius:8px;border:1px solid var(--border)">
  </div>
</div>
@endif

<div class="card">
  <div class="card-title">Riwayat Bulan Ini</div>
  <table>
    <thead><tr><th>Tanggal</th><th>Status</th><th>Masuk</th><th>Pulang</th><th>Lembur (jam)</th></tr></thead>
    <tbody>
      @forelse($records as $r)
      <tr>
        <td>{{ $r->date->format('d/m/Y') }}</td>
        <td><span class="badge {{ $r->statusBadge() }}">{{ $r->statusLabel() }}</span></td>
        <td>{{ $r->clock_in?->format('H:i') ?? '-' }}</td>
        <td>{{ $r->clock_out?->format('H:i') ?? '-' }}</td>
        <td>{{ $r->overtime_hours ?: '-' }}</td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada riwayat absensi bulan ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Modal notifikasi hasil scan QR -- muncul otomatis begitu petugas security memindai. --}}
<div id="checkinModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center">
  <div class="card" style="max-width:360px;width:90%;margin:0;text-align:center">
    <div style="font-size:2.5rem;margin-bottom:.5rem">✅</div>
    <div class="card-title" id="checkinModalTitle">Absen Tersimpan</div>
    <div id="checkinModalText" style="margin-bottom:1rem"></div>
    <button type="button" class="btn btn-primary" onclick="acknowledgeCheckin()">OK</button>
  </div>
</div>

@push('scripts')
<script>
// Device fingerprint -- diadopsi verbatim dari computeDeviceFingerprint() di cscallpro
// (customer.php/call.php): canvas/WebGL/screen/timezone/hardwareConcurrency di-hash SHA-256
// di client. Dipakai AttendanceController::clockIn()/clockOut() buat "auto scan" absensi
// mandiri tanpa security -- absen pertama mendaftarkan device ini, absen berikutnya wajib
// cocok. Best-effort (bukan identifier yang mustahil dipalsukan), tapi jauh menyulitkan joki
// lewat berbagi akun ke device lain.
var DEVICE_FINGERPRINT_PROMISE = (async function computeDeviceFingerprint() {
  try {
    var parts = [
      navigator.userAgent || '', navigator.language || '', navigator.platform || '',
      String(navigator.maxTouchPoints || 0),
      screen.width + 'x' + screen.height + 'x' + screen.colorDepth,
      (Intl.DateTimeFormat().resolvedOptions().timeZone || ''),
      String(navigator.hardwareConcurrency || '')
    ];
    try {
      var gl = document.createElement('canvas').getContext('webgl') || document.createElement('canvas').getContext('experimental-webgl');
      var dbg = gl && gl.getExtension('WEBGL_debug_renderer_info');
      if (dbg) { parts.push(gl.getParameter(dbg.UNMASKED_VENDOR_WEBGL) || ''); parts.push(gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL) || ''); }
    } catch (e) {}
    var buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(parts.join('||')));
    return Array.from(new Uint8Array(buf)).map(function (b) { return b.toString(16).padStart(2, '0'); }).join('');
  } catch (e) { return ''; }
})();

DEVICE_FINGERPRINT_PROMISE.then(function (fp) {
  document.querySelectorAll('.device-fp-field').forEach(function (el) { el.value = fp; });
});
</script>
@endpush

@if($checkin)
@push('scripts')
<script>
(function(){
  var QR_IMG_URL = @json(route('attendances.checkin.qr', $checkin));
  var STATUS_URL = @json(route('attendances.checkin.status'));

  // Anti-joki -- di-refresh tiap 30 detik selama halaman ini terbuka, supaya "qr_shown_at"
  // di server tetap baru (lihat AttendanceScanController::isQrFresh()). Sama pola dengan QR
  // check-in pasien di klinikita.
  setInterval(function () {
    var img = document.getElementById('attendanceQrImg');
    if (img) img.src = QR_IMG_URL + '?_=' + Date.now();
  }, 30000);

  var modal = document.getElementById('checkinModal');
  var modalText = document.getElementById('checkinModalText');
  var timer = setInterval(pollStatus, 5000);

  function pollStatus() {
    fetch(STATUS_URL, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
      .then(function (data) {
        var checkin = data.checkin;
        if (checkin && checkin.status === 'scanned') {
          clearInterval(timer);
          modalText.innerHTML = 'Absen <strong>' + checkin.clock_type_label + '</strong> tersimpan, jam <strong>' + checkin.time + '</strong>.';
          modal.style.display = 'flex';
        }
      })
      .catch(function () { /* diamkan -- dicoba lagi di siklus polling berikutnya */ });
  }

  window.acknowledgeCheckin = function () {
    modal.style.display = 'none';
    window.location.reload();
  };
})();
</script>
@endpush
@endif
@endif
@endsection
