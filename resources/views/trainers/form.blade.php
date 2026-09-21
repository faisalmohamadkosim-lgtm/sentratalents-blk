@extends('layouts.app')
@section('title', isset($trainer) ? 'Edit Trainer' : 'Tambah Trainer')
@section('content')
@php $isEdit = isset($trainer); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Trainer' : 'Tambah Trainer' }}</div>
  <a href="{{ route('trainers.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('trainers.update', $trainer) : route('trainers.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Nama *</label>
        <input type="text" name="name" value="{{ old('name', $trainer->name ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Spesialisasi</label>
        <input type="text" name="specialization" value="{{ old('specialization', $trainer->specialization ?? '') }}" placeholder="mis. Las & Fabrikasi Logam">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $trainer->email ?? '') }}">
      </div>
      <div class="form-group">
        <label>No. HP</label>
        <input type="tel" name="phone" value="{{ old('phone', $trainer->phone ?? '') }}">
      </div>
      <div class="form-group full">
        <label>Bio</label>
        <textarea name="bio">{{ old('bio', $trainer->bio ?? '') }}</textarea>
      </div>
      <div class="form-group">
        <label>Foto</label>
        @if($isEdit && $trainer->photoUrl())
        <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
          <img src="{{ $trainer->photoUrl() }}" style="height:48px;width:48px;object-fit:cover;border-radius:50%">
          <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
            <input type="checkbox" name="delete_photo" value="1"> Hapus foto
          </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/*">
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $trainer->active ?? true))>
        <label for="active" style="margin:0">Aktif</label>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Trainer' }}</button>
      <a href="{{ route('trainers.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
