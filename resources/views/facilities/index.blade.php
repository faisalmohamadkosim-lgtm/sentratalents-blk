@extends('layouts.app')
@section('title','Fasilitas')
@section('content')

<div class="page-header">
  <div class="page-title">Fasilitas</div>
  <a href="{{ route('facilities.create') }}" class="btn btn-primary">+ Tambah Fasilitas</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama fasilitas..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('facilities.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th style="width:64px">Foto</th><th>Nama</th><th>Deskripsi</th><th style="width:70px">Urutan</th><th></th></tr></thead>
    <tbody>
      @forelse($facilities as $f)
      <tr>
        <td>
          @if($f->photoUrl())
            <img src="{{ $f->photoUrl() }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
          @else
            <div style="width:48px;height:48px;border-radius:6px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3)">🏭</div>
          @endif
        </td>
        <td style="font-weight:600">{{ $f->name }}</td>
        <td style="font-size:.82rem;color:var(--text2)">{{ Str::limit($f->description ?? '-', 80) }}</td>
        <td>{{ $f->sort_order }}</td>
        <td style="white-space:nowrap">
          <a href="{{ route('facilities.edit', $f) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('facilities.destroy', $f) }}" style="display:inline" onsubmit="return confirm('Hapus fasilitas ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:2rem">Belum ada fasilitas.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($facilities->hasPages())
  <div class="pagination">{!! $facilities->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
