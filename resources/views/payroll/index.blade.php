@extends('layouts.app')
@section('title', 'Payroll')
@section('content')

<div class="page-header">
  <div class="page-title">Payroll</div>
  <a href="{{ route('payroll.create') }}" class="btn btn-primary">+ Generate Payroll</a>
</div>

<div class="card">
  <table>
    <thead><tr><th>Periode</th><th>Jml Pegawai</th><th style="text-align:right">Total Take-Home</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($periods as $p)
      <tr>
        <td>{{ $p->label() }}</td>
        <td>{{ $p->items_count }}</td>
        <td style="text-align:right">Rp {{ number_format($p->total_net, 0, ',', '.') }}</td>
        <td><span class="badge {{ $p->isFinalized() ? 'badge-green' : 'badge-yellow' }}">{{ $p->isFinalized() ? 'Final' : 'Draft' }}</span></td>
        <td><a href="{{ route('payroll.show', $p) }}" class="btn btn-outline btn-sm">Detail</a></td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada periode payroll.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($periods->hasPages())
  <div class="pagination">{!! $periods->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
