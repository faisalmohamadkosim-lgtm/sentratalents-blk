@extends('layouts.app')
@section('title', 'Payroll ' . $period->label())
@section('content')
@php $role = auth()->user()->role; @endphp

<div class="page-header">
  <div class="page-title">
    Payroll {{ $period->label() }}
    <span class="badge {{ $period->isFinalized() ? 'badge-green' : 'badge-yellow' }}">{{ $period->isFinalized() ? 'Final' : 'Draft' }}</span>
  </div>
  <div style="display:flex;gap:.5rem">
    @if(! $period->isFinalized())
    <form method="POST" action="{{ route('payroll.finalize', $period) }}" onsubmit="return confirm('Finalisasi payroll periode ini? Setelah final, rincian gaji tidak dapat diubah lagi dan siap dibayarkan.')">
      @csrf
      <button type="submit" class="btn btn-success">🔒 Finalisasi</button>
    </form>
    @endif
    <a href="{{ route('payroll.index') }}" class="btn btn-outline">← Daftar Payroll</a>
  </div>
</div>

<div class="card">
  <div class="form-grid" style="font-size:.85rem">
    <div><strong>Jumlah Pegawai:</strong> {{ $period->items->count() }}</div>
    <div><strong>Total Gaji Bruto:</strong> Rp {{ number_format($period->total_gross, 0, ',', '.') }}</div>
    <div><strong>Total Take-Home (Netto):</strong> Rp {{ number_format($period->total_net, 0, ',', '.') }}</div>
    <div><strong>Total Estimasi PPh21:</strong> Rp {{ number_format($period->items->sum('pph21_amount'), 0, ',', '.') }}</div>
    <div><strong>Total Biaya BPJS Perusahaan:</strong> Rp {{ number_format($period->items->sum(fn($i) => $i->bpjs_kesehatan_employer + $i->bpjs_jht_employer + $i->bpjs_jp_employer + $i->bpjs_jkk_employer + $i->bpjs_jkm_employer), 0, ',', '.') }}</div>
    <div><strong>Digenerate Oleh:</strong> {{ $period->generatedBy->name ?? '-' }} pada {{ $period->generated_at?->format('d/m/Y H:i') }}</div>
    @if($period->isFinalized())
    <div><strong>Difinalisasi Oleh:</strong> {{ $period->finalizedBy->name ?? '-' }} pada {{ $period->finalized_at?->format('d/m/Y H:i') }}</div>
    @endif
  </div>
  <div style="margin-top:.75rem;padding:.6rem .9rem;background:#fef9c3;border-radius:8px;color:#854d0e;font-size:.78rem">
    ⚠ Estimasi BPJS &amp; PPh21 (metode TER, PMK 168/2023) dihitung otomatis sebagai perkiraan internal, bukan pengganti aplikasi pajak resmi. Verifikasi ke akuntan/konsultan pajak sebelum digunakan untuk pelaporan resmi.
  </div>
</div>

@if(! $period->isFinalized())
<div class="card">
  <div class="card-title">Rincian Gaji per Pegawai — Draft</div>
  <div class="text-muted" style="margin-bottom:1rem">Isi Potongan Lain (misal kasbon/pinjaman) bila perlu, lalu simpan. Setelah difinalisasi, rincian tidak dapat diubah lagi.</div>
  <form method="POST" action="{{ route('payroll.deductions.update', $period) }}">
    @csrf
    <div style="overflow-x:auto">
    <table>
      <thead>
        <tr>
          <th>Pegawai</th><th style="text-align:right">Bruto</th><th style="text-align:right">BPJS+PPh21+Absen</th>
          <th style="width:150px">Potongan Lain (Rp)</th><th style="width:180px">Catatan</th><th style="text-align:right">Netto</th>
        </tr>
      </thead>
      <tbody>
        @foreach($period->items as $item)
        <tr>
          <td>{{ $item->employee_name }} <span class="text-muted">({{ $item->position }})</span></td>
          <td style="text-align:right">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td>
          <td style="text-align:right">Rp {{ number_format($item->absence_deduction + $item->bpjs_kesehatan_employee + $item->bpjs_jht_employee + $item->bpjs_jp_employee + $item->pph21_amount, 0, ',', '.') }}</td>
          <td><input type="number" name="other_deduction[{{ $item->id }}]" value="{{ $item->other_deduction }}" min="0" step="1000"></td>
          <td><input type="text" name="other_deduction_notes[{{ $item->id }}]" value="{{ $item->other_deduction_notes }}"></td>
          <td style="text-align:right;font-weight:700">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <button type="submit" class="btn btn-primary" style="margin-top:1rem">💾 Simpan Potongan Lain</button>
  </form>
</div>
@else
<div class="card">
  <div class="card-title">Rincian Gaji per Pegawai</div>
  <div style="overflow-x:auto">
  <table>
    <thead>
      <tr><th>Pegawai</th><th style="text-align:right">Bruto</th><th style="text-align:right">Potongan</th><th style="text-align:right">Netto</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      @foreach($period->items as $item)
      <tr>
        <td>{{ $item->employee_name }} <span class="text-muted">({{ $item->position }})</span></td>
        <td style="text-align:right">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td>
        <td style="text-align:right">Rp {{ number_format($item->total_deduction, 0, ',', '.') }}</td>
        <td style="text-align:right;font-weight:700">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
        <td>
          @if($item->isPaid())
          <span class="badge badge-green">✔ Dibayar</span>
          @else
          <span class="badge badge-orange">Belum Dibayar</span>
          @endif
        </td>
        <td style="white-space:nowrap">
          <a href="{{ route('payroll-items.payslip', $item) }}" class="btn btn-outline btn-sm">📄 Slip</a>
          @if(! $item->isPaid())
          <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('pay-form-{{ $item->id }}').hidden = false; this.hidden = true;">💵 Bayar</button>
          @endif
        </td>
      </tr>
      @if(! $item->isPaid())
      <tr id="pay-form-{{ $item->id }}" hidden>
        <td colspan="6" style="background:var(--bg)">
          <form method="POST" action="{{ route('payroll.items.pay', [$period, $item]) }}" enctype="multipart/form-data" style="display:flex;gap:.5rem;align-items:end;flex-wrap:wrap;padding:.5rem 0">
            @csrf
            <div class="form-group" style="margin:0">
              <label>Metode</label>
              <select name="payment_method" required>
                <option value="transfer">Transfer Bank</option>
                <option value="tunai">Tunai</option>
              </select>
            </div>
            <div class="form-group" style="margin:0">
              <label>Tanggal Bayar</label>
              <input type="date" name="paid_date" value="{{ now()->toDateString() }}" required>
            </div>
            <div class="form-group" style="margin:0">
              <label>Bukti (opsional)</label>
              <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf">
            </div>
            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Catat gaji {{ $item->employee_name }} sudah dibayar?')">✔ Konfirmasi Bayar</button>
          </form>
        </td>
      </tr>
      @endif
      @endforeach
    </tbody>
  </table>
  </div>
</div>
@endif
@endsection
