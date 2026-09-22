<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Scan QR Absensi — {{ $tenant->name }}</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0d6efd;--bg:#f1f5f9;--surface:#fff;
  --text1:#0f172a;--text2:#475569;--text3:#94a3b8;--border:#e2e8f0;--radius:10px;
}
@media(prefers-color-scheme:dark){:root{
  --bg:#0f172a;--surface:#1e293b;--text1:#f1f5f9;--text2:#94a3b8;--text3:#475569;--border:#334155;
}}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text1);
  min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:1.5rem 1rem}
.kiosk-header{display:flex;align-items:center;gap:.6rem;margin-bottom:1.25rem}
.kiosk-logo{width:40px;height:40px;border-radius:9px;background:var(--primary);color:#fff;
  display:flex;align-items:center;justify-content:center;font-weight:800;overflow:hidden;flex-shrink:0}
.kiosk-logo img{width:100%;height:100%;object-fit:cover}
.kiosk-title{font-size:1.1rem;font-weight:800}
.kiosk-sub{font-size:.75rem;color:var(--text3);text-transform:uppercase;letter-spacing:.05em}
.card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:1.25rem;margin-bottom:1.25rem}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .9rem;border-radius:7px;
  font-size:.82rem;font-weight:600;cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-decoration:none;white-space:nowrap}
.btn:hover{filter:brightness(.93)}
.btn-outline{background:transparent;color:var(--text1);border-color:var(--border)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}
.btn-sm{padding:.3rem .65rem;font-size:.75rem}
.kiosk-footer{color:var(--text3);font-size:.72rem;margin-top:.5rem;text-align:center}
</style>
</head>
<body>

<div class="kiosk-header">
  <div class="kiosk-logo">
    @if($tenant->logoUrl())<img src="{{ $tenant->logoUrl() }}" alt="">@else{{ strtoupper(substr($tenant->name,0,1)) }}@endif
  </div>
  <div>
    <div class="kiosk-title">Scan QR Absensi</div>
    <div class="kiosk-sub">{{ $tenant->name }}</div>
  </div>
</div>

@include('attendance-scan._scanner', ['handleUrl' => route('public.attendance-scan.handle', $tenant->slug)])

<div class="kiosk-footer">Halaman kiosk publik -- tidak perlu login.</div>

</body>
</html>
