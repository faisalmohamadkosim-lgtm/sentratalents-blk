@extends('layouts.app')
@section('title','Angkatan')
@section('content')

<div class="page-header">
  <div class="page-title">Angkatan</div>
  <a href="{{ route('batches.create') }}" class="btn btn-primary">+ Tambah Angkatan</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode / program..." style="max-width:240px">
    <select name="status" style="max-width:180px">
      <option value="">Semua Status</option>
      @foreach(\App\Models\Batch::STATUSES as $s)
        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search || $status)<a href="{{ route('batches.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>Kode</th><th>Program</th><th>Jadwal</th><th style="text-align:right">Peserta</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($batches as $b)
      <tr>
        <td style="font-weight:600">{{ $b->code }}</td>
        <td style="font-size:.82rem">{{ $b->program->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $b->start_date?->format('d/m/Y') ?? '-' }} — {{ $b->end_date?->format('d/m/Y') ?? '-' }}</td>
        <td style="text-align:right">{{ $b->enrollments_count }}</td>
        <td><span class="badge {{ match($b->status) { 'ongoing' => 'badge-blue', 'completed' => 'badge-green', 'cancelled' => 'badge-red', default => 'badge-gray' } }}">{{ $b->status }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('batches.show', $b) }}" class="btn btn-outline btn-sm">Detail</a>
          <a href="{{ route('batches.edit', $b) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('batches.destroy', $b) }}" style="display:inline" onsubmit="return confirm('Hapus angkatan ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Belum ada angkatan.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($batches->hasPages())
  <div class="pagination">{!! $batches->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
