@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('content')

<div class="page-header">
  <div class="page-title">Data Karyawan</div>
  <a href="{{ route('employees.create') }}" class="btn btn-primary">+ Tambah Karyawan</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.4rem;margin-bottom:1rem;flex-wrap:wrap">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / no. karyawan / jabatan..." style="flex:1;min-width:200px">
    <select name="status" style="max-width:200px">
      <option value="">Semua Status</option>
      <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
      <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
      <option value="resigned" {{ $status === 'resigned' ? 'selected' : '' }}>Resign</option>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
  </form>
  <table>
    <thead><tr><th style="width:44px">Foto</th><th>No. Karyawan</th><th>Nama</th><th>Jabatan</th><th>Tipe</th><th style="text-align:right">Gaji Pokok</th><th>Status</th><th>Perangkat Absen</th><th></th></tr></thead>
    <tbody>
      @forelse($employees as $emp)
      <tr>
        <td>
          @if($emp->photoUrl())
            <img src="{{ $emp->photoUrl() }}" style="width:36px;height:36px;object-fit:cover;border-radius:50%">
          @else
            <div style="width:36px;height:36px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:.9rem">🧑</div>
          @endif
        </td>
        <td>{{ $emp->employee_no }}</td>
        <td>{{ $emp->name }}</td>
        <td>{{ $emp->position }}</td>
        <td>{{ $emp->employmentTypeLabel() }}</td>
        <td style="text-align:right">Rp {{ number_format($emp->base_salary, 0, ',', '.') }}</td>
        <td><span class="badge {{ $emp->statusBadge() }}">{{ $emp->statusLabel() }}</span></td>
        <td>
          @if($emp->device_fingerprint)
            <span class="badge badge-blue">Terdaftar</span>
            <form method="POST" action="{{ route('employees.reset-device', $emp) }}" style="display:inline" onsubmit="return confirm('Reset perangkat terdaftar {{ $emp->name }}? Absen berikutnya akan mendaftarkan perangkat baru.')">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm">Reset</button>
            </form>
          @else
            <span class="badge badge-gray">Belum ada</span>
          @endif
        </td>
        <td><a href="{{ route('employees.edit', $emp) }}" class="btn btn-outline btn-sm">Edit</a></td>
      </tr>
      @empty
      <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada data karyawan.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($employees->hasPages())
  <div class="pagination">{!! $employees->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
