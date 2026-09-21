@extends('layouts.app')
@section('title', $batch->code)
@section('content')

<div class="page-header">
  <div class="page-title">Angkatan {{ $batch->code }} — {{ $batch->program->name ?? '-' }}</div>
  <div style="display:flex;gap:.5rem">
    <a href="{{ route('batches.edit', $batch) }}" class="btn btn-outline btn-sm">Edit</a>
    <a href="{{ route('batches.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
</div>

<div class="card">
  <div class="form-grid">
    <div><span class="text-muted">Program</span><div style="margin-top:.25rem;font-weight:600">{{ $batch->program->name ?? '-' }}</div></div>
    <div><span class="text-muted">Status</span><div style="margin-top:.25rem"><span class="badge {{ match($batch->status) { 'ongoing' => 'badge-blue', 'completed' => 'badge-green', 'cancelled' => 'badge-red', default => 'badge-gray' } }}">{{ $batch->status }}</span></div></div>
    <div><span class="text-muted">Tanggal Mulai</span><div style="margin-top:.25rem">{{ $batch->start_date?->format('d/m/Y') ?? '-' }}</div></div>
    <div><span class="text-muted">Tanggal Selesai</span><div style="margin-top:.25rem">{{ $batch->end_date?->format('d/m/Y') ?? '-' }}</div></div>
    <div><span class="text-muted">Kuota</span><div style="margin-top:.25rem">{{ $batch->quota ?? '-' }}</div></div>
    <div><span class="text-muted">Jumlah Peserta Terdaftar</span><div style="margin-top:.25rem;font-weight:600">{{ $batch->participantCount() }}</div></div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="display:flex;align-items:center;justify-content:space-between">
    Peserta di Angkatan Ini
    <a href="{{ route('enrollments.create') }}?batch_id={{ $batch->id }}" class="btn btn-primary btn-sm">+ Daftarkan Peserta</a>
  </div>
  @if($batch->enrollments->isEmpty())
    <p style="color:var(--text3);font-size:.85rem">Belum ada peserta terdaftar.</p>
  @else
  <table>
    <thead><tr><th>Nama Peserta</th><th>NIK</th><th>Status</th><th>Nilai Akhir</th><th>Tgl Lulus</th><th></th></tr></thead>
    <tbody>
      @foreach($batch->enrollments as $enrollment)
      <tr>
        <td><a href="{{ route('participants.show', $enrollment->participant) }}">{{ $enrollment->participant->name ?? '-' }}</a></td>
        <td style="font-size:.8rem;color:var(--text2)">{{ $enrollment->participant->nik ?? '-' }}</td>
        <td><span class="badge {{ $enrollment->statusBadge() }}">{{ $enrollment->status }}</span></td>
        <td>{{ $enrollment->final_score ?? '-' }}</td>
        <td>{{ $enrollment->graduation_date?->format('d/m/Y') ?? '-' }}</td>
        <td><a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-outline btn-sm">Edit</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection
