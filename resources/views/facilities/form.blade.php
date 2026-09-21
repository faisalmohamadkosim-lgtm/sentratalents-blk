@extends('layouts.app')
@section('title', isset($facility) ? 'Edit Fasilitas' : 'Tambah Fasilitas')
@section('content')
@php $isEdit = isset($facility); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}</div>
  <a href="{{ route('facilities.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('facilities.update', $facility) : route('facilities.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group full">
        <label>Nama Fasilitas *</label>
        <input type="text" name="name" value="{{ old('name', $facility->name ?? '') }}" required>
      </div>
      <div class="form-group full">
        <label>Deskripsi</label>
        <textarea name="description">{{ old('description', $facility->description ?? '') }}</textarea>
      </div>
      <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $facility->sort_order ?? 0) }}" min="0">
      </div>
      <div class="form-group">
        <label>Foto</label>
        @if($isEdit && $facility->photoUrl())
        <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
          <img src="{{ $facility->photoUrl() }}" style="height:48px;object-fit:cover;border-radius:6px">
          <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
            <input type="checkbox" name="delete_photo" value="1"> Hapus foto
          </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/*">
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Fasilitas' }}</button>
      <a href="{{ route('facilities.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
