@extends('layouts.superadmin')
@section('title', isset($photo) ? 'Edit Foto Galeri' : 'Tambah Foto Galeri')
@section('content')
@php $isEdit = isset($photo); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' }}</div>
  <a href="{{ route('superadmin.gallery.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:560px">
  <form method="POST" action="{{ $isEdit ? route('superadmin.gallery.update', $photo) : route('superadmin.gallery.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-group">
      <label>Foto {{ $isEdit ? '' : '*' }}</label>
      @if($isEdit && $photo->photoUrl())
      <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
        <img src="{{ $photo->photoUrl() }}" style="height:56px;width:80px;object-fit:cover;border-radius:6px">
        <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
          <input type="checkbox" name="delete_photo" value="1"> Hapus foto
        </label>
      </div>
      @endif
      <input type="file" name="photo" accept="image/*" {{ $isEdit ? '' : 'required' }}>
      <div class="text-muted" style="margin-top:.3rem">Rasio disarankan 4:3, maks 2MB.</div>
    </div>

    <div class="form-group">
      <label>Caption</label>
      <input type="text" name="caption" value="{{ old('caption', $photo->caption ?? '') }}" placeholder="mis. Praktik lapangan peserta pelatihan">
    </div>

    <div class="form-group">
      <label>Tautan (opsional)</label>
      <input type="text" name="link_url" value="{{ old('link_url', $photo->link_url ?? '') }}" placeholder="mis. /blk/blk-bandung atau https://...">
      <div class="text-muted" style="margin-top:.3rem">Kosongkan supaya foto tidak bisa diklik. Bisa path relatif (mis. <code>/blk/nama-slug</code> atau <code>/lulusan</code>) atau URL lengkap.</div>
    </div>

    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $photo->sort_order ?? 0) }}" style="max-width:120px">
    </div>

    <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $photo->active ?? true))>
      <label for="active" style="margin:0">Aktif (tampil di landing page)</label>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Foto' }}</button>
      <a href="{{ route('superadmin.gallery.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
