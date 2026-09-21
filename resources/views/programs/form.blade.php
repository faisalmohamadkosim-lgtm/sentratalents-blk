@extends('layouts.app')
@section('title', isset($program) ? 'Edit Program' : 'Tambah Program')
@section('content')
@php $isEdit = isset($program); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Program' : 'Tambah Program' }}</div>
  <a href="{{ $isEdit ? route('programs.show', $program) : route('programs.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('programs.update', $program) : route('programs.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Nama Program *</label>
        <input type="text" name="name" value="{{ old('name', $program->name ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Kategori/Kejuruan</label>
        <input type="text" name="category" value="{{ old('category', $program->category ?? '') }}" placeholder="mis. Teknik Manufaktur">
      </div>
      <div class="form-group">
        <label>Trainer Utama</label>
        <select name="lead_trainer_id">
          <option value="">-- Belum ditentukan --</option>
          @foreach($trainers as $tr)
            <option value="{{ $tr->id }}" @selected(old('lead_trainer_id', $program->lead_trainer_id ?? '') == $tr->id)>{{ $tr->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Durasi (hari)</label>
        <input type="number" name="duration_days" value="{{ old('duration_days', $program->duration_days ?? '') }}" min="0">
      </div>
      <div class="form-group">
        <label>Kuota per Angkatan</label>
        <input type="number" name="quota" value="{{ old('quota', $program->quota ?? '') }}" min="0">
      </div>
      <div class="form-group">
        <label>Foto</label>
        @if($isEdit && $program->photoUrl())
        <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
          <img src="{{ $program->photoUrl() }}" style="height:48px;object-fit:cover;border-radius:6px">
          <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
            <input type="checkbox" name="delete_photo" value="1"> Hapus foto
          </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/*">
      </div>
      <div class="form-group full">
        <label>Deskripsi</label>
        <textarea name="description">{{ old('description', $program->description ?? '') }}</textarea>
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $program->active ?? true))>
        <label for="active" style="margin:0">Aktif</label>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Program' }}</button>
      <a href="{{ $isEdit ? route('programs.show', $program) : route('programs.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
