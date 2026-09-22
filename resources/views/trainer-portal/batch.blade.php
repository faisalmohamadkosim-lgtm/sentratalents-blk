@extends('layouts.app')
@section('title','Peserta Angkatan '.$batch->code)
@section('content')

<div class="page-header">
  <div class="page-title">Peserta — {{ $batch->program->name }} ({{ $batch->code }})</div>
  <a href="{{ route('trainer-portal.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <table>
    <thead><tr><th>Peserta</th><th>Status</th><th>Tgl Lulus</th><th>No. Sertifikat</th></tr></thead>
    <tbody>
      @forelse($enrollments as $e)
      <tr>
        <td style="font-weight:600">{{ $e->participant->name ?? '-' }}</td>
        <td><span class="badge {{ $e->statusBadge() }}">{{ $e->status }}</span></td>
        <td style="font-size:.82rem">{{ $e->graduation_date?->format('d/m/Y') ?? '-' }}</td>
        <td style="font-size:.78rem;font-family:monospace">{{ $e->certificate_no ?? '-' }}</td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:var(--text3);padding:2rem">Belum ada peserta di angkatan ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
