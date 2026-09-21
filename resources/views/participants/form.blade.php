@extends('layouts.app')
@section('title', isset($participant) ? 'Edit Peserta' : 'Tambah Peserta')
@section('content')
@php $isEdit = isset($participant); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Peserta' : 'Tambah Peserta' }}</div>
  <a href="{{ route('participants.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('participants.update', $participant) : route('participants.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group" style="display:flex;flex-direction:column;align-items:center;gap:.5rem">
        <div style="width:100px;height:100px;border-radius:50%;border:3px dashed var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;background:var(--surface2)">
          @if($isEdit && $participant->photoUrl())
            <img src="{{ $participant->photoUrl() }}" style="width:100%;height:100%;object-fit:cover">
          @else
            <span style="font-size:2.2rem">👤</span>
          @endif
        </div>
        <input type="file" name="photo" accept="image/*" style="max-width:180px">
        @if($isEdit && $participant->photoUrl())
        <label style="font-size:.72rem;cursor:pointer;display:flex;align-items:center;gap:.3rem">
          <input type="checkbox" name="delete_photo" value="1"> Hapus foto
        </label>
        @endif
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;align-content:start" class="full">
        <div class="form-group full">
          <label>Nama Lengkap *</label>
          <input type="text" name="name" value="{{ old('name', $participant->name ?? '') }}" required>
        </div>
        <div class="form-group">
          <label>NIK</label>
          <input type="text" name="nik" value="{{ old('nik', $participant->nik ?? '') }}" maxlength="30">
        </div>
        <div class="form-group">
          <label>Jenis Kelamin</label>
          <select name="gender">
            <option value="">-- Pilih --</option>
            <option value="L" @selected(old('gender', $participant->gender ?? '') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender', $participant->gender ?? '') === 'P')>Perempuan</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal Lahir</label>
          <input type="date" name="birth_date" value="{{ old('birth_date', ($participant->birth_date ?? null)?->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
          <label>Pendidikan Terakhir</label>
          <input type="text" name="education" value="{{ old('education', $participant->education ?? '') }}" placeholder="mis. SMA/SMK">
        </div>
        <div class="form-group">
          <label>No. HP</label>
          <input type="tel" name="phone" value="{{ old('phone', $participant->phone ?? '') }}">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email', $participant->email ?? '') }}">
        </div>
        <div class="form-group full">
          <label>Alamat</label>
          <textarea name="address">{{ old('address', $participant->address ?? '') }}</textarea>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Peserta' }}</button>
      <a href="{{ route('participants.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
