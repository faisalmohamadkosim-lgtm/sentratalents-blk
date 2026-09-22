<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 28px 36px; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color:#1e293b; }
table.kop { width:100%; border-bottom: 3px solid {{ $color = '#0d6efd' }}; padding-bottom:10px; margin-bottom:14px; }
table.kop td { vertical-align: top; }
table.kop .logo-cell { width:44px; padding-right:10px; }
table.kop .logo-cell img { width:38px; height:38px; object-fit:contain; }
.kop-name { font-size: 17px; font-weight: bold; color: {{ $color }}; }
.kop-detail { font-size: 9px; color:#64748b; line-height:1.5; padding-top:3px; }
.title { text-align:center; margin: 6px 0 2px; font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
.subnumber { text-align:center; font-size: 10px; color:#64748b; margin-bottom:14px; }
table.info { width:100%; margin-bottom:14px; }
table.info td { padding:2px 0; font-size:10px; vertical-align:top; }
.info-label { width:110px; color:#64748b; }
.section-title { font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:{{ $color }}; margin:12px 0 6px; border-bottom:1px solid #e2e8f0; padding-bottom:3px; }
table.comp { width:100%; border-collapse:collapse; margin-bottom:10px; }
table.comp th { background:{{ $color }}; color:#fff; padding:6px 8px; text-align:left; font-size:9px; font-weight:bold; }
table.comp td { padding:5px 8px; border-bottom:1px solid #e2e8f0; font-size:10px; }
table.comp tr.total-row td { font-weight:bold; border-top: 1px solid {{ $color }}; }
.net-box { background:#eff6ff; border:1px solid {{ $color }}; border-radius:6px; padding:12px 14px; margin:14px 0; text-align:center; }
.net-box .nl { font-size:10px; color:#64748b; text-transform:uppercase; letter-spacing:.5px; }
.net-box .nv { font-size:20px; font-weight:bold; color:{{ $color }}; margin-top:2px; }
.disclaimer { font-size:8px; color:#94a3b8; margin-top:16px; line-height:1.5; }
table.sign { width:100%; margin-top:24px; }
table.sign td { width:50%; vertical-align:top; font-size:10px; }
.sign-space { height:50px; }
.sign-line { border-top:1px solid #334155; padding-top:4px; width:200px; }
</style>
</head>
<body>

<table class="kop">
  <tr>
    @if($cfg->logoUrl())
    <td class="logo-cell"><img src="{{ $cfg->logoUrl() }}" alt="Logo"></td>
    @endif
    <td>
      <div class="kop-name">{{ $cfg->name ?? 'BLK Connect' }}</div>
      <div class="kop-detail">
        {{ $cfg->address ?? '' }}
        @if($cfg->phone ?? null) &nbsp;|&nbsp; Tel: {{ $cfg->phone }} @endif
        @if($cfg->email ?? null) &nbsp;|&nbsp; {{ $cfg->email }} @endif
      </div>
    </td>
  </tr>
</table>

<div class="title">Slip Gaji</div>
<div class="subnumber">Periode {{ $item->period->label() }}</div>

<table class="info">
  <tr>
    <td style="width:50%">
      <table><tr><td class="info-label">Nama</td><td>: {{ $item->employee_name }}</td></tr></table>
      <table><tr><td class="info-label">Jabatan</td><td>: {{ $item->position ?? '-' }}</td></tr></table>
      <table><tr><td class="info-label">Status PTKP</td><td>: {{ $item->ptkp_status ?? '-' }}</td></tr></table>
    </td>
    <td style="width:50%">
      <table><tr><td class="info-label">Hari Kerja</td><td>: {{ $item->work_days }} hari</td></tr></table>
      <table><tr><td class="info-label">Hadir / Alpha</td><td>: {{ $item->present_days }} / {{ $item->absent_days }} hari</td></tr></table>
      <table><tr><td class="info-label">Lembur</td><td>: {{ $item->overtime_hours }} jam</td></tr></table>
    </td>
  </tr>
</table>

<div class="section-title">Pendapatan</div>
<table class="comp">
  <tbody>
    <tr><td>Gaji Pokok</td><td style="text-align:right">Rp {{ number_format($item->base_salary, 0, ',', '.') }}</td></tr>
    @if($item->allowance_position > 0)<tr><td>Tunjangan Jabatan</td><td style="text-align:right">Rp {{ number_format($item->allowance_position, 0, ',', '.') }}</td></tr>@endif
    @if($item->allowance_transport > 0)<tr><td>Tunjangan Transport</td><td style="text-align:right">Rp {{ number_format($item->allowance_transport, 0, ',', '.') }}</td></tr>@endif
    @if($item->allowance_meal > 0)<tr><td>Tunjangan Makan</td><td style="text-align:right">Rp {{ number_format($item->allowance_meal, 0, ',', '.') }}</td></tr>@endif
    @if($item->allowance_other > 0)<tr><td>Tunjangan Lain-lain</td><td style="text-align:right">Rp {{ number_format($item->allowance_other, 0, ',', '.') }}</td></tr>@endif
    @if($item->overtime_amount > 0)<tr><td>Lembur ({{ $item->overtime_hours }} jam)</td><td style="text-align:right">Rp {{ number_format($item->overtime_amount, 0, ',', '.') }}</td></tr>@endif
    <tr class="total-row"><td>Total Pendapatan (Bruto)</td><td style="text-align:right">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td></tr>
  </tbody>
</table>

<div class="section-title">Potongan</div>
<table class="comp">
  <tbody>
    @if($item->absence_deduction > 0)<tr><td>Potongan Ketidakhadiran ({{ $item->absent_days }} hari)</td><td style="text-align:right">Rp {{ number_format($item->absence_deduction, 0, ',', '.') }}</td></tr>@endif
    @if($item->bpjs_kesehatan_employee > 0)<tr><td>BPJS Kesehatan</td><td style="text-align:right">Rp {{ number_format($item->bpjs_kesehatan_employee, 0, ',', '.') }}</td></tr>@endif
    @if($item->bpjs_jht_employee > 0)<tr><td>BPJS JHT</td><td style="text-align:right">Rp {{ number_format($item->bpjs_jht_employee, 0, ',', '.') }}</td></tr>@endif
    @if($item->bpjs_jp_employee > 0)<tr><td>BPJS JP</td><td style="text-align:right">Rp {{ number_format($item->bpjs_jp_employee, 0, ',', '.') }}</td></tr>@endif
    <tr><td>Estimasi PPh21 (TER {{ $item->pph21_ter_category }}, {{ number_format($item->pph21_rate * 100, 2) }}%)</td><td style="text-align:right">Rp {{ number_format($item->pph21_amount, 0, ',', '.') }}</td></tr>
    @if($item->other_deduction > 0)<tr><td>Potongan Lain {{ $item->other_deduction_notes ? '('.$item->other_deduction_notes.')' : '' }}</td><td style="text-align:right">Rp {{ number_format($item->other_deduction, 0, ',', '.') }}</td></tr>@endif
    <tr class="total-row"><td>Total Potongan</td><td style="text-align:right">Rp {{ number_format($item->total_deduction, 0, ',', '.') }}</td></tr>
  </tbody>
</table>

<div class="net-box">
  <div class="nl">Take-Home Pay (Netto)</div>
  <div class="nv">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</div>
</div>

@if($item->isPaid())
<div style="font-size:10px;margin-bottom:6px">Dibayar via {{ $item->paymentMethodLabel() }} pada {{ $item->paid_date?->format('d F Y') }}.</div>
@endif

<table class="sign">
  <tr>
    <td>
      Diterima Oleh,
      <div class="sign-space"></div>
      <div class="sign-line">{{ $item->employee_name }}</div>
    </td>
    <td>
      Dibuat Oleh,
      <div class="sign-space"></div>
      <div class="sign-line">{{ $cfg->name ?? 'BLK Connect' }}</div>
    </td>
  </tr>
</table>

<div class="disclaimer">
  Perhitungan BPJS &amp; PPh21 pada slip ini adalah estimasi internal (metode TER sesuai PMK 168/2023), bukan dokumen resmi perpajakan/BPJS. Mohon verifikasi ke akuntan/konsultan pajak untuk kebutuhan pelaporan resmi.
</div>

</body>
</html>
