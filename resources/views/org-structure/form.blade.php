@extends('layouts.app')
@section('title', isset($member) ? 'Edit Anggota' : 'Tambah Anggota')
@section('content')
@php $isEdit = isset($member); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Anggota Struktur' : 'Tambah Anggota Struktur' }}</div>
  <a href="{{ route('org-structure.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('org-structure.update', $member) : route('org-structure.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Nama *</label>
        <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Jabatan *</label>
        <input type="text" name="position" value="{{ old('position', $member->position ?? '') }}" placeholder="mis. Kepala BLK" required>
      </div>
      <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $member->sort_order ?? 0) }}" min="0">
      </div>
      <div class="form-group">
        <label>Foto</label>
        @if($isEdit && $member->photoUrl())
        <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
          <img src="{{ $member->photoUrl() }}" style="height:48px;width:48px;object-fit:cover;border-radius:50%">
          <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
            <input type="checkbox" name="delete_photo" value="1"> Hapus foto
          </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/*">
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Anggota' }}</button>
      <a href="{{ route('org-structure.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
