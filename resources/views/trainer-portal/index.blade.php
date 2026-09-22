@extends('layouts.app')
@section('title','Program Saya')
@section('content')

<div class="page-header">
  <div class="page-title">Program yang Diampu — {{ $trainer->name }}</div>
</div>

@forelse($trainer->leadPrograms as $program)
<div class="card">
  <div class="card-title">{{ $program->name }} <span class="text-muted" style="font-weight:400">({{ $program->category ?? '-' }})</span></div>
  @if($program->description)<p class="text-muted" style="margin-bottom:1rem">{{ $program->description }}</p>@endif

  <table>
    <thead><tr><th>Angkatan</th><th>Jadwal</th><th>Status</th><th>Peserta</th><th></th></tr></thead>
    <tbody>
      @forelse($program->batches as $batch)
      <tr>
        <td style="font-weight:600">{{ $batch->code }}</td>
        <td style="font-size:.82rem">{{ $batch->start_date?->format('d/m/Y') }} — {{ $batch->end_date?->format('d/m/Y') }}</td>
        <td><span class="badge {{ $batch->status === 'ongoing' ? 'badge-yellow' : ($batch->status === 'completed' ? 'badge-green' : 'badge-gray') }}">{{ $batch->status }}</span></td>
        <td>{{ $batch->participantCount() }}</td>
        <td><a href="{{ route('trainer-portal.batch', $batch) }}" class="btn btn-outline btn-sm">Lihat Peserta</a></td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada angkatan untuk program ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@empty
<div class="card">
  <div class="empty" style="text-align:center;color:var(--text3);padding:2rem">Anda belum ditugaskan sebagai pengampu program manapun.</div>
</div>
@endforelse
@endsection
