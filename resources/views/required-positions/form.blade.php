@extends('layouts.app')
@section('title', isset($position) ? 'Edit Jabatan Minimal' : 'Tambah Jabatan Minimal')
@section('content')
@php $isEdit = isset($position); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Jabatan Minimal' : 'Tambah Jabatan Minimal' }}</div>
  <a href="{{ route('required-positions.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('required-positions.update', $position) : route('required-positions.store') }}">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="form-grid">
      <div class="form-group full">
        <label>Nama Jabatan *</label>
        <input type="text" name="position_name" value="{{ old('position_name', $position->position_name ?? '') }}" placeholder="Contoh: Instruktur/Trainer, Tenaga Administrasi" required>
        <div class="text-muted" style="margin-top:.25rem">Dicocokkan otomatis ke jabatan Karyawan yang mengandung teks ini (tidak case-sensitive). Kalau mengandung kata "instruktur"/"trainer", akun Trainer aktif ikut dihitung.</div>
      </div>
      <div class="form-group">
        <label>Jumlah Minimal *</label>
        <input type="number" name="min_count" value="{{ old('min_count', $position->min_count ?? 1) }}" min="1" max="255" required>
      </div>
      <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
        <label style="display:flex;align-items:center;gap:.4rem;margin:0">
          <input type="checkbox" name="active" value="1" style="width:auto" {{ old('active', $position->active ?? true) ? 'checked' : '' }}> Aktif dipantau
        </label>
      </div>
      <div class="form-group full">
        <label>Catatan</label>
        <textarea name="notes">{{ old('notes', $position->notes ?? '') }}</textarea>
      </div>
    </div>
    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan' : 'Tambah' }}</button>
      <a href="{{ route('required-positions.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
