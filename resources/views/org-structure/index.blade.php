@extends('layouts.app')
@section('title','Struktur Organisasi')
@section('content')

<div class="page-header">
  <div class="page-title">Struktur Organisasi</div>
  <a href="{{ route('org-structure.create') }}" class="btn btn-primary">+ Tambah Anggota</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / jabatan..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('org-structure.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th style="width:56px">Foto</th><th>Nama</th><th>Jabatan</th><th style="width:70px">Urutan</th><th></th></tr></thead>
    <tbody>
      @forelse($members as $m)
      <tr>
        <td>
          @if($m->photoUrl())
            <img src="{{ $m->photoUrl() }}" style="width:40px;height:40px;object-fit:cover;border-radius:50%">
          @else
            <div style="width:40px;height:40px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3)">🗂️</div>
          @endif
        </td>
        <td style="font-weight:600">{{ $m->name }}</td>
        <td style="font-size:.82rem;color:var(--text2)">{{ $m->position }}</td>
        <td>{{ $m->sort_order }}</td>
        <td style="white-space:nowrap">
          <a href="{{ route('org-structure.edit', $m) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('org-structure.destroy', $m) }}" style="display:inline" onsubmit="return confirm('Hapus anggota ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:2rem">Belum ada anggota struktur organisasi.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($members->hasPages())
  <div class="pagination">{!! $members->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
