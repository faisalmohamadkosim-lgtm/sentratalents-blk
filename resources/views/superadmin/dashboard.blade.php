@extends('layouts.superadmin')
@section('title','Ringkasan')
@section('content')

<div class="page-header">
  <div class="page-title">Ringkasan Seluruh Tenant BLK</div>
  <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">+ Daftarkan BLK</a>
</div>

<div class="stat-grid">
  <div class="stat-card" style="border-left:4px solid var(--primary)">
    <div class="stat-label">Total Tenant BLK</div>
    <div class="stat-value">{{ $tenants->count() }}</div>
  </div>
  <div class="stat-card" style="border-left:4px solid var(--success)">
    <div class="stat-label">Total Peserta</div>
    <div class="stat-value">{{ number_format($totalParticipants) }}</div>
  </div>
  <div class="stat-card" style="border-left:4px solid var(--info)">
    <div class="stat-label">Total Lulusan</div>
    <div class="stat-value">{{ number_format($tenants->sum('graduates')) }}</div>
  </div>
  <div class="stat-card" style="border-left:4px solid #7c3aed">
    <div class="stat-label">Total User Aktif</div>
    <div class="stat-value">{{ number_format($tenants->sum('active_users')) }}</div>
  </div>
</div>

<div class="card">
  <div class="card-title">Distribusi Status Pendaftaran</div>
  @if ($enrollmentsByStatus->sum() > 0)
    @php
      $statusLabels = ['registered' => 'Terdaftar', 'active' => 'Aktif', 'graduated' => 'Lulus', 'dropped_out' => 'Mengundurkan Diri'];
      $statusColors = ['registered' => 'badge-blue', 'active' => 'badge-yellow', 'graduated' => 'badge-green', 'dropped_out' => 'badge-red'];
      $maxCnt = $enrollmentsByStatus->max();
    @endphp
    @foreach($enrollmentsByStatus as $status => $cnt)
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;font-size:.82rem">
      <div style="width:150px"><span class="badge {{ $statusColors[$status] ?? 'badge-gray' }}">{{ $statusLabels[$status] ?? $status }}</span></div>
      <div style="flex:1;background:var(--surface2);border-radius:4px;height:16px;overflow:hidden">
        <div style="height:100%;border-radius:4px;background:var(--primary);width:{{ $maxCnt > 0 ? round($cnt/$maxCnt*100) : 0 }}%"></div>
      </div>
      <div style="width:36px;text-align:right;font-weight:700">{{ $cnt }}</div>
    </div>
    @endforeach
  @else
    <p style="color:var(--text3);font-size:.85rem">Belum ada data.</p>
  @endif
</div>

<div class="card">
  <div class="card-title">Ringkasan per Tenant BLK</div>
  <div style="overflow-x:auto">
  <table>
    <thead>
      <tr>
        <th>BLK</th>
        <th>Kode</th>
        <th style="text-align:right">Peserta</th>
        <th style="text-align:right">Program</th>
        <th style="text-align:right">Lulusan</th>
        <th style="text-align:right">User Aktif</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($tenants as $row)
      <tr>
        <td style="font-weight:600">{{ $row['tenant']->name }}</td>
        <td style="font-family:monospace;font-size:.8rem">{{ $row['tenant']->code ?? '—' }}</td>
        <td style="text-align:right">{{ number_format($row['tenant']->participants_count) }}</td>
        <td style="text-align:right">{{ number_format($row['tenant']->programs_count) }}</td>
        <td style="text-align:right">{{ number_format($row['graduates']) }}</td>
        <td style="text-align:right">{{ number_format($row['active_users']) }}</td>
        <td><a href="{{ route('superadmin.tenants.edit', $row['tenant']) }}" class="btn btn-outline btn-sm">Kelola</a></td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:2rem">Belum ada BLK terdaftar.</td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
@endsection
