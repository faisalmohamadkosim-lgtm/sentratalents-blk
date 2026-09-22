@extends('layouts.app')
@section('title', 'Slip Gaji Saya')
@section('content')

<div class="page-header">
  <div class="page-title">Slip Gaji Saya</div>
</div>

@if(! $employee)
<div class="card">
  <div class="text-muted">Akun Anda belum ditautkan ke data karyawan. Hubungi Admin/Owner untuk menautkan akun Anda ke data karyawan.</div>
</div>
@else
<div class="card">
  <table>
    <thead><tr><th>Periode</th><th style="text-align:right">Bruto</th><th style="text-align:right">Potongan</th><th style="text-align:right">Netto</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($items as $item)
      <tr>
        <td>{{ $item->period->label() }}</td>
        <td style="text-align:right">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td>
        <td style="text-align:right">Rp {{ number_format($item->total_deduction, 0, ',', '.') }}</td>
        <td style="text-align:right;font-weight:700">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
        <td><span class="badge {{ $item->isPaid() ? 'badge-green' : 'badge-orange' }}">{{ $item->isPaid() ? '✔ Dibayar' : 'Belum Dibayar' }}</span></td>
        <td><a href="{{ route('payroll-items.payslip', $item) }}" class="btn btn-outline btn-sm">📄 Unduh Slip</a></td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada slip gaji.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endif
@endsection
