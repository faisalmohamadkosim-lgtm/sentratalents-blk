@extends('layouts.app')
@section('title', isset($role) ? 'Edit Role' : 'Tambah Role')
@section('content')
@php $isEdit = isset($role); $isSystem = $isEdit && in_array($role->name, ['superadmin','ultraadmin','admin','owner','staff']); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Role' : 'Tambah Role' }}</div>
  <a href="{{ route('roles.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:520px">
  <form method="POST" action="{{ $isEdit ? route('roles.update', $role->id) : route('roles.store') }}">
    @csrf @if($isEdit) @method('PUT') @endif

    <div class="form-group">
      <label>Nama Role * <span style="font-size:.72rem;color:var(--text3)">(kode sistem, huruf kecil)</span></label>
      <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}"
             {{ $isSystem ? 'readonly' : '' }} required placeholder="nama_role">
      @if($isSystem)
      <div style="font-size:.75rem;color:var(--text3);margin-top:.2rem">Role bawaan sistem — nama tidak dapat diubah.</div>
      @else
      <div style="font-size:.75rem;color:var(--text3);margin-top:.2rem">Huruf kecil, tanpa spasi (contoh: koordinator)</div>
      @endif
    </div>

    <div class="form-group">
      <label>Label Tampilan <span style="font-size:.72rem;color:var(--text3)">(nama yang terlihat pengguna)</span></label>
      <input type="text" name="label" value="{{ old('label', $role->label ?? '') }}" placeholder="Koordinator Program">
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" rows="2" placeholder="Tanggung jawab role ini...">{{ old('description', $role->description ?? '') }}</textarea>
    </div>

    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem;margin-bottom:1rem;padding:.75rem;background:var(--surface2);border-radius:8px;border:1px solid var(--border)">
      <input type="checkbox" name="can_admin" value="1" {{ old('can_admin', $role->can_admin ?? false) ? 'checked' : '' }} style="width:auto">
      <span><strong>Can Admin</strong> — akses menu administrasi (pengguna, profil BLK)</span>
    </label>

    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem;margin-bottom:1rem">
      <input type="checkbox" name="active" value="1" {{ old('active', $role->active ?? true) ? 'checked' : '' }} style="width:auto">
      <span>Role aktif</span>
    </label>

    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan' : 'Tambah Role' }}</button>
  </form>
</div>
@endsection
