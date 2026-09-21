@extends('layouts.app')
@section('title','Trainer/Instruktur')
@section('content')

<div class="page-header">
  <div class="page-title">Trainer/Instruktur</div>
  <a href="{{ route('trainers.create') }}" class="btn btn-primary">+ Tambah Trainer</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / spesialisasi..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('trainers.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th style="width:56px">Foto</th><th>Nama</th><th>Spesialisasi</th><th>Kontak</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($trainers as $t)
      <tr>
        <td>
          @if($t->photoUrl())
            <img src="{{ $t->photoUrl() }}" style="width:40px;height:40px;object-fit:cover;border-radius:50%">
          @else
            <div style="width:40px;height:40px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3)">🧑‍🏫</div>
          @endif
        </td>
        <td style="font-weight:600">{{ $t->name }}</td>
        <td style="font-size:.82rem;color:var(--text2)">{{ $t->specialization ?? '-' }}</td>
        <td style="font-size:.8rem;color:var(--text2)">{{ $t->email ?? $t->phone ?? '-' }}</td>
        <td><span class="badge {{ $t->active ? 'badge-green' : 'badge-gray' }}">{{ $t->active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('trainers.edit', $t) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('trainers.destroy', $t) }}" style="display:inline" onsubmit="return confirm('Hapus trainer ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Belum ada trainer.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($trainers->hasPages())
  <div class="pagination">{!! $trainers->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
