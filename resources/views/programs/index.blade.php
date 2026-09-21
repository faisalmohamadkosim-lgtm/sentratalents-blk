@extends('layouts.app')
@section('title','Program')
@section('content')

<div class="page-header">
  <div class="page-title">Program</div>
  <a href="{{ route('programs.create') }}" class="btn btn-primary">+ Tambah Program</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / kategori..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('programs.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>Nama</th><th>Kategori</th><th>Trainer Utama</th><th>Durasi</th><th>Angkatan</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($programs as $p)
      <tr>
        <td><a href="{{ route('programs.show', $p) }}" style="font-weight:600">{{ $p->name }}</a></td>
        <td style="font-size:.82rem;color:var(--text2)">{{ $p->category ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $p->leadTrainer?->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $p->duration_days ? $p->duration_days.' hari' : '-' }}</td>
        <td style="text-align:center">{{ $p->batches_count }}</td>
        <td><span class="badge {{ $p->active ? 'badge-green' : 'badge-gray' }}">{{ $p->active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('programs.show', $p) }}" class="btn btn-outline btn-sm">Detail</a>
          <a href="{{ route('programs.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('programs.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus program ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:2rem">Belum ada program.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($programs->hasPages())
  <div class="pagination">{!! $programs->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
