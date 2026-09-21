@extends('layouts.app')
@section('title', $participant->name)
@section('content')

<div class="page-header">
  <div class="page-title">{{ $participant->name }}</div>
  <div style="display:flex;gap:.5rem">
    <a href="{{ route('participants.edit', $participant) }}" class="btn btn-outline btn-sm">Edit</a>
    <a href="{{ route('participants.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
</div>

<div class="card">
  <div style="display:flex;align-items:flex-start;gap:1.25rem">
    <div style="width:110px;height:110px;border-radius:50%;overflow:hidden;flex-shrink:0;border:3px solid var(--border);display:flex;align-items:center;justify-content:center;background:var(--surface2)">
      @if($participant->photoUrl())
        <img src="{{ $participant->photoUrl() }}" style="width:100%;height:100%;object-fit:cover">
      @else
        <span style="font-size:2.5rem">👤</span>
      @endif
    </div>
    <div class="form-grid" style="flex:1">
      <div><span class="text-muted">Nama</span><div style="margin-top:.25rem;font-weight:600">{{ $participant->name }}</div></div>
      <div><span class="text-muted">NIK</span><div style="margin-top:.25rem">{{ $participant->nik ?? '-' }}</div></div>
      <div><span class="text-muted">Jenis Kelamin</span><div style="margin-top:.25rem">{{ $participant->gender === 'L' ? 'Laki-laki' : ($participant->gender === 'P' ? 'Perempuan' : '-') }}</div></div>
      <div><span class="text-muted">Tanggal Lahir</span><div style="margin-top:.25rem">{{ $participant->birth_date?->format('d/m/Y') ?? '-' }}</div></div>
      <div><span class="text-muted">Pendidikan</span><div style="margin-top:.25rem">{{ $participant->education ?? '-' }}</div></div>
      <div><span class="text-muted">No. HP</span><div style="margin-top:.25rem">{{ $participant->phone ?? '-' }}</div></div>
      <div class="full"><span class="text-muted">Alamat</span><div style="margin-top:.25rem">{{ $participant->address ?? '-' }}</div></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="display:flex;align-items:center;justify-content:space-between">
    Riwayat Pendaftaran / Pelatihan
    <a href="{{ route('enrollments.create') }}?participant_id={{ $participant->id }}" class="btn btn-primary btn-sm">+ Daftarkan</a>
  </div>
  @if($participant->enrollments->isEmpty())
    <p style="color:var(--text3);font-size:.85rem">Belum ada pendaftaran.</p>
  @else
  <table>
    <thead><tr><th>Program</th><th>Angkatan</th><th>Status</th><th>Nilai Akhir</th><th>Tgl Lulus</th><th>No. Sertifikat</th><th></th></tr></thead>
    <tbody>
      @foreach($participant->enrollments->sortByDesc('created_at') as $enrollment)
      <tr>
        <td style="font-size:.82rem">{{ $enrollment->batch->program->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $enrollment->batch->code ?? '-' }}</td>
        <td><span class="badge {{ $enrollment->statusBadge() }}">{{ $enrollment->status }}</span></td>
        <td>{{ $enrollment->final_score ?? '-' }}</td>
        <td>{{ $enrollment->graduation_date?->format('d/m/Y') ?? '-' }}</td>
        <td style="font-family:monospace;font-size:.78rem">{{ $enrollment->certificate_no ?? '-' }}</td>
        <td><a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-outline btn-sm">Edit</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection
