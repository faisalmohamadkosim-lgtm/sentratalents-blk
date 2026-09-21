@extends('layouts.app')
@section('title','Peserta Kami')
@section('content')

<div class="page-header">
  <div class="page-title">Peserta yang Dikirim — {{ $company->name }}</div>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <select name="status">
      <option value="">Semua Status</option>
      @foreach(\App\Models\Enrollment::STATUSES as $s)
        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Filter</button>
    @if($status)<a href="{{ route('company-portal.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>Peserta</th><th>Program</th><th>Angkatan</th><th>Status</th><th>Tgl Lulus</th><th>No. Sertifikat</th></tr></thead>
    <tbody>
      @forelse($enrollments as $e)
      <tr>
        <td style="font-weight:600">{{ $e->participant->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $e->batch->program->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $e->batch->code ?? '-' }}</td>
        <td><span class="badge {{ $e->statusBadge() }}">{{ $e->status }}</span></td>
        <td style="font-size:.82rem">{{ $e->graduation_date?->format('d/m/Y') ?? '-' }}</td>
        <td style="font-size:.78rem;font-family:monospace">{{ $e->certificate_no ?? '-' }}</td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Belum ada peserta yang dikirim.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($enrollments->hasPages())
  <div class="pagination">{!! $enrollments->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
