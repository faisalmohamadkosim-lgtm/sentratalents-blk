@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="page-header">
  <div class="page-title">Dashboard</div>
  <div style="font-size:.78rem;color:var(--text3)">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Total Program</div>
    <div class="stat-value">{{ number_format($stats['programs']) }}</div>
    <div class="stat-icon">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Peserta</div>
    <div class="stat-value">{{ number_format($stats['participants']) }}</div>
    <div class="stat-icon">👤</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Angkatan Berjalan</div>
    <div class="stat-value">{{ number_format($stats['active_batches']) }}</div>
    <div class="stat-icon">📅</div>
  </div>
  <div class="stat-card" style="border-left:4px solid var(--success)">
    <div class="stat-label">Total Lulusan</div>
    <div class="stat-value" style="color:var(--success)">{{ number_format($stats['graduates']) }}</div>
    <div class="stat-icon">🎓</div>
  </div>
</div>

@if(isset($batchesByStatus) && $batchesByStatus->sum() > 0)
<div class="card">
  <div class="card-title" style="margin-bottom:.75rem">Distribusi Status Angkatan</div>
  @php
    $statusLabels = ['planned' => 'Rencana', 'ongoing' => 'Berjalan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusColors = ['planned' => 'badge-gray', 'ongoing' => 'badge-blue', 'completed' => 'badge-green', 'cancelled' => 'badge-red'];
    $maxB = $batchesByStatus->max();
  @endphp
  @foreach($batchesByStatus as $status => $cnt)
  <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;font-size:.82rem">
    <div style="width:130px"><span class="badge {{ $statusColors[$status] ?? 'badge-gray' }}">{{ $statusLabels[$status] ?? $status }}</span></div>
    <div style="flex:1;background:var(--surface2);border-radius:4px;height:16px;overflow:hidden">
      <div style="height:100%;border-radius:4px;background:var(--primary);width:{{ $maxB > 0 ? round($cnt/$maxB*100) : 0 }}%"></div>
    </div>
    <div style="width:36px;text-align:right;font-weight:700">{{ $cnt }}</div>
  </div>
  @endforeach
</div>
@endif

<div class="card">
  <div class="card-title">Pendaftaran Terbaru</div>
  @if($recentEnrollments->isEmpty())
    <p style="color:var(--text3);font-size:.85rem">Belum ada pendaftaran.</p>
  @else
  <table>
    <thead>
      <tr>
        <th>Peserta</th>
        <th>Program</th>
        <th>Angkatan</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($recentEnrollments as $e)
      <tr>
        <td><a href="{{ route('participants.show', $e->participant) }}">{{ $e->participant->name ?? '-' }}</a></td>
        <td style="font-size:.82rem">{{ $e->batch->program->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $e->batch->code ?? '-' }}</td>
        <td><span class="badge {{ $e->statusBadge() }}">{{ $e->status }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection
