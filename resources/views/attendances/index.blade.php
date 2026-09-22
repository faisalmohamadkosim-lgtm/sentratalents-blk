@extends('layouts.app')
@section('title', 'Absensi Pegawai')
@section('content')

<div class="page-header">
  <div class="page-title">Absensi Pegawai</div>
  <a href="{{ route('attendances.create') }}" class="btn btn-primary">+ Tambah Absensi</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.4rem;margin-bottom:1rem;flex-wrap:wrap">
    <select name="month" style="max-width:160px">
      @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
      <option value="{{ $i+1 }}" {{ $month == $i+1 ? 'selected' : '' }}>{{ $namaBulan }}</option>
      @endforeach
    </select>
    <input type="number" name="year" value="{{ $year }}" style="max-width:100px" min="2020" max="2100">
    <select name="employee_id" style="max-width:220px">
      <option value="">Semua Pegawai</option>
      @foreach($employees as $emp)
      <option value="{{ $emp->id }}" {{ (string) $employeeId === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Tampilkan</button>
  </form>
  <table>
    <thead><tr><th>Tanggal</th><th>Pegawai</th><th>Status</th><th>Masuk</th><th>Pulang</th><th>Lembur</th><th></th></tr></thead>
    <tbody>
      @forelse($records as $r)
      <tr>
        <td>{{ $r->date->format('d/m/Y') }}</td>
        <td>{{ $r->employee->name ?? '-' }}</td>
        <td><span class="badge {{ $r->statusBadge() }}">{{ $r->statusLabel() }}</span></td>
        <td>{{ $r->clock_in?->format('H:i') ?? '-' }}</td>
        <td>{{ $r->clock_out?->format('H:i') ?? '-' }}</td>
        <td>{{ $r->overtime_hours ?: '-' }}</td>
        <td style="white-space:nowrap">
          <a href="{{ route('attendances.edit', $r) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('attendances.destroy', $r) }}" style="display:inline" onsubmit="return confirm('Hapus catatan absensi ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada data absensi untuk periode ini.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($records->hasPages())
  <div class="pagination">{!! $records->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
