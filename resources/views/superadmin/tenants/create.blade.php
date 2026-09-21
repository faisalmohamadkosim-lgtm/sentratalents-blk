@extends('layouts.superadmin')
@section('title','Daftarkan BLK')
@section('content')

<div class="page-header">
  <div class="page-title">Daftarkan BLK Baru</div>
  <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:640px">
  <form method="POST" action="{{ route('superadmin.tenants.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group full">
      <label>Nama BLK *</label>
      <input type="text" name="name" value="{{ old('name') }}" required autofocus>
    </div>
    <div class="form-group full">
      <label>Deskripsi / Sambutan</label>
      <textarea name="description" rows="3">{{ old('description') }}</textarea>
    </div>
    <div class="form-group full">
      <label>Alamat</label>
      <textarea name="address">{{ old('address') }}</textarea>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label>Kota</label>
        <input type="text" name="city" value="{{ old('city') }}">
      </div>
      <div class="form-group">
        <label>Provinsi</label>
        <input type="text" name="province" value="{{ old('province') }}">
      </div>
      <div class="form-group">
        <label>Telepon</label>
        <input type="tel" name="phone" value="{{ old('phone') }}">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
      </div>
      <div class="form-group">
        <label>Website</label>
        <input type="url" name="website" value="{{ old('website') }}" placeholder="https://blk.id">
      </div>
      <div class="form-group">
        <label>Kode BLK</label>
        <input type="text" name="code" value="{{ old('code') }}" placeholder="mis. dari Kemnaker">
      </div>
      <div class="form-group">
        <label>Tahun Berdiri</label>
        <input type="number" name="established_year" value="{{ old('established_year') }}" min="1900" max="{{ date('Y') + 1 }}">
      </div>
      <div class="form-group">
        <label>Akreditasi</label>
        <input type="text" name="accreditation" value="{{ old('accreditation') }}" placeholder="mis. Terakreditasi A">
      </div>
    </div>
    <div class="form-group full">
      <label>Logo</label>
      <input type="file" name="logo" accept="image/*">
    </div>
    <div class="form-group full">
      <label>Gambar Sampul</label>
      <input type="file" name="cover_image" accept="image/*">
    </div>
    <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" id="active" name="active" value="1" checked style="width:auto">
      <label for="active" style="margin:0">Aktif</label>
    </div>
    <div class="text-muted" style="margin-bottom:1rem">Slug URL profil publik (/blk/...) dibuat otomatis dari nama BLK.</div>
    <button type="submit" class="btn btn-primary">Daftarkan BLK</button>
  </form>
</div>
@endsection
