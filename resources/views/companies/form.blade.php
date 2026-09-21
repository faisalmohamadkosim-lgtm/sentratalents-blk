@extends('layouts.app')
@section('title', isset($company) ? 'Edit Perusahaan' : 'Tambah Perusahaan')
@section('content')
@php $isEdit = isset($company); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Perusahaan' : 'Tambah Perusahaan' }}</div>
  <a href="{{ route('companies.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('companies.update', $company) : route('companies.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Nama Perusahaan *</label>
        <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Narahubung</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person ?? '') }}">
      </div>
      <div class="form-group">
        <label>No. Telepon</label>
        <input type="tel" name="phone" value="{{ old('phone', $company->phone ?? '') }}">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}">
      </div>
      <div class="form-group full">
        <label>Alamat</label>
        <input type="text" name="address" value="{{ old('address', $company->address ?? '') }}">
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $company->active ?? true))>
        <label for="active" style="margin:0">Aktif</label>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Perusahaan' }}</button>
      <a href="{{ route('companies.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>

@if($isEdit)
<div class="card" style="max-width:520px">
  <div class="card-title">Akun Login Perusahaan</div>
  <p style="font-size:.82rem;color:var(--text2);margin-bottom:1rem">Buat akun login untuk perwakilan {{ $company->name }} supaya bisa memantau status peserta yang mereka kirim.</p>
  <a href="{{ route('companies.users.create', $company) }}" class="btn btn-outline">+ Buat Akun Login</a>
</div>
@endif
@endsection
