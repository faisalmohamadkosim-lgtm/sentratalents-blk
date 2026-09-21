@extends('layouts.app')
@section('title','Galeri')
@section('content')

<div class="page-header">
  <div class="page-title">Galeri</div>
</div>

<div class="card">
  <div class="card-title">+ Tambah Foto</div>
  <form method="POST" action="{{ route('gallery.store') }}" enctype="multipart/form-data" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end">
    @csrf
    <div class="form-group" style="margin-bottom:0">
      <label>Foto *</label>
      <input type="file" name="photo" accept="image/*" required>
    </div>
    <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
      <label>Keterangan</label>
      <input type="text" name="caption" placeholder="mis. Kegiatan pelatihan las 2026">
    </div>
    <div class="form-group" style="margin-bottom:0;width:100px">
      <label>Urutan</label>
      <input type="number" name="sort_order" min="0">
    </div>
    <button type="submit" class="btn btn-primary">Unggah</button>
  </form>
</div>

<div class="card">
  <div class="card-title">Foto Galeri</div>
  @if($photos->isEmpty())
    <p style="color:var(--text3);font-size:.85rem">Belum ada foto galeri.</p>
  @else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem">
    @foreach($photos as $photo)
    <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--surface2)">
      <img src="{{ $photo->photoUrl() }}" style="width:100%;height:120px;object-fit:cover">
      <div style="padding:.5rem">
        <div style="font-size:.78rem;color:var(--text2);margin-bottom:.5rem;min-height:1.2em">{{ $photo->caption ?? '-' }}</div>
        <form method="POST" action="{{ route('gallery.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini?')">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm" style="width:100%">Hapus</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  @if($photos->hasPages())
  <div class="pagination">{!! $photos->links('vendor.pagination.simple') !!}</div>
  @endif
  @endif
</div>
@endsection
